<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Funder;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Project::with('funder');

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('name', 'LIKE', "%{$search}%")
                  ->orWhereHas('funder', function ($q) use ($search) {
                      $q->where('name', 'LIKE', "%{$search}%");
                  });
        }

        $projects = $query->latest()->paginate(10);
        return view('projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $funders = Funder::all();
        return view('projects.create', compact('funders'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'funder_id' => 'required|exists:funders,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'description' => 'nullable|string',
        ]);

        $project = new Project($request->all());
        $project->value = 0;
        $project->currency = 'USD'; // Set a default currency
        $project->save();

        return redirect()->route('projects.index')
                         ->with('success', __('main.project_added_successfully'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Project $project)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $currencyFilter = $request->input('currency');
        $print = $request->input('print', false);

        $donationsQuery = $project->donations();
        $paymentVouchersQuery = $project->paymentVouchers();
        $expenseVouchersQuery = $project->expenseVouchers();

        if ($startDate) {
            $donationsQuery->where('date', '>=', $startDate);
            $paymentVouchersQuery->where('date', '>=', $startDate);
            $expenseVouchersQuery->where('date', '>=', $startDate);
        }
        if ($endDate) {
            $donationsQuery->where('date', '<=', $endDate);
            $paymentVouchersQuery->where('date', '<=', $endDate);
            $expenseVouchersQuery->where('date', '<=', $endDate);
        }
        if ($currencyFilter) {
            $donationsQuery->where('currency', $currencyFilter);
            $paymentVouchersQuery->where('currency', $currencyFilter);
            $expenseVouchersQuery->where('currency', $currencyFilter);
        }

        $donations = $donationsQuery->get();
        $paymentVouchers = $paymentVouchersQuery->get();
        $expenseVouchers = $expenseVouchersQuery->get();

        $allTransactions = $donations->concat($paymentVouchers)->concat($expenseVouchers);
        $currencyTotals = [];

        foreach ($allTransactions as $transaction) {
            $currencyCode = $transaction->currency;
            if (!isset($currencyTotals[$currencyCode])) {
                $currencyTotals[$currencyCode] = [
                    'total_income' => 0,
                    'total_expenses' => 0,
                    'net_balance' => 0,
                    'currency_symbol' => getCurrencyName($currencyCode, true)
                ];
            }

            $netAmount = $transaction->amount - ($transaction->association_fee ?? 0);

            if ($transaction instanceof \App\Models\Donation || $transaction instanceof \App\Models\PaymentVoucher) {
                $currencyTotals[$currencyCode]['total_income'] += $netAmount;
            } else {
                $currencyTotals[$currencyCode]['total_expenses'] += $netAmount;
            }
        }
        
        foreach ($currencyTotals as $code => &$totals) {
            $totals['net_balance'] = $totals['total_income'] - $totals['total_expenses'];
        }

        $incomeTransactions = $donations->map(function ($item) {
            $item->transaction_type_display = __('main.donation');
            $item->transaction_party = $item->donor_name;
            $item->transaction_link = route('donations.show', $item);
            $item->credit = $item->amount - $item->association_fee;
            $item->debit = 0;
            return $item;
        })->concat($paymentVouchers->map(function ($item) {
            $item->transaction_type_display = __('main.payment_voucher');
            $item->transaction_party = $item->payer;
            $item->transaction_link = route('payment-vouchers.show', $item);
            $item->credit = $item->amount - $item->association_fee;
            $item->debit = 0;
            return $item;
        }));

        $expenseTransactions = $expenseVouchers->map(function ($item) {
            $item->transaction_type_display = __('main.expense_voucher');
            $item->transaction_party = $item->recipient;
            $item->transaction_link = route('expense-vouchers.show', $item);
            $item->credit = 0;
            $item->debit = $item->amount - $item->association_fee;
            return $item;
        });

        $allTransactions = $incomeTransactions->concat($expenseTransactions)->sortByDesc('date');
        $availableCurrencies = $project->currencies()->pluck('code');

        return view('projects.show', compact('project', 'allTransactions', 'currencyTotals', 'startDate', 'endDate', 'print', 'currencyFilter', 'availableCurrencies'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        if ($project->is_default) {
            return redirect()->route('projects.index')->with('error', __('main.cannot_edit_default_project'));
        }
        $funders = Funder::all();
        return view('projects.edit', compact('project', 'funders'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        if ($project->is_default) {
            return redirect()->route('projects.index')->with('error', __('main.cannot_edit_default_project'));
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'funder_id' => 'required|exists:funders,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'description' => 'nullable|string',
        ]);

        $project->update($request->all());

        return redirect()->route('projects.index')
                         ->with('success', __('main.project_updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        if ($project->is_default) {
            return redirect()->route('projects.index')->with('error', __('main.cannot_delete_default_project'));
        }
        $project->delete();

        return redirect()->route('projects.index')
                         ->with('success', __('main.project_deleted_successfully'));
    }
}
