<?php

namespace App\Http\Controllers;

use App\Models\CashBox;
use App\Models\Currency;
use Illuminate\Http\Request;

class CashBoxController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = CashBox::with('currencies');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $cashBoxes = $query->latest()->paginate(10);
        
        return view('cash-boxes.index', compact('cashBoxes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $currencies = Currency::all();
        return view('cash-boxes.create', compact('currencies'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:cash_boxes,name',
            'location' => 'nullable|string|max:255',
            'manager' => 'nullable|string|max:255',
            'currencies' => 'required|array|min:1',
            'currencies.*.id' => 'required|exists:currencies,id',
            'currencies.*.balance' => 'required|numeric|min:0',
        ]);

        $cashBox = CashBox::create($request->only('name', 'location', 'manager'));

        $currencyData = [];
        foreach ($request->currencies as $currency) {
            $currencyData[$currency['id']] = ['balance' => $currency['balance']];
        }
        $cashBox->currencies()->attach($currencyData);

        return redirect()->route('cash-boxes.index')
                         ->with('success', __('main.cash_box_added_successfully'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, CashBox $cashBox)
    {
        $donationsQuery = $cashBox->donations();
        $paymentVouchersQuery = $cashBox->paymentVouchers();
        $expenseVouchersQuery = $cashBox->expenseVouchers();

        if ($request->filled('type')) {
            switch ($request->type) {
                case 'donation':
                    $paymentVouchersQuery->whereRaw('1 = 0'); // Empty query
                    $expenseVouchersQuery->whereRaw('1 = 0'); // Empty query
                    break;
                case 'payment_voucher':
                    $donationsQuery->whereRaw('1 = 0');
                    $expenseVouchersQuery->whereRaw('1 = 0');
                    break;
                case 'expense_voucher':
                    $donationsQuery->whereRaw('1 = 0');
                    $paymentVouchersQuery->whereRaw('1 = 0');
                    break;
            }
        }
        
        $donations = $donationsQuery->get()->map(function ($item) {
            $item->transaction_type = __('main.donation');
            $item->transaction_details = $item->donor_name;
            $item->payment_method_name = __('main.payment_method_' . $item->payment_method);
            $item->credit = $item->amount;
            $item->debit = 0;
            return $item;
        });

        $paymentVouchers = $paymentVouchersQuery->get()->map(function ($item) {
            $item->transaction_type = __('main.payment_voucher');
            $item->transaction_details = $item->payer;
            $item->payment_method_name = __('main.payment_method_' . $item->payment_method);
            $item->credit = $item->amount;
            $item->debit = 0;
            return $item;
        });

        $expenseVouchers = $expenseVouchersQuery->get()->map(function ($item) {
            $item->transaction_type = __('main.expense_voucher');
            $item->transaction_details = $item->recipient;
            $item->payment_method_name = __('main.payment_method_' . $item->payment_method);
            $item->credit = 0;
            $item->debit = $item->amount;
            return $item;
        });

        $transactions = $donations->concat($paymentVouchers)->concat($expenseVouchers)->sortByDesc('date');

        $balances = $cashBox->currencies()->get()->mapWithKeys(function ($currency) {
            return [$currency->code => $currency->pivot->balance];
        });
        
        return view('cash-boxes.show', compact('cashBox', 'transactions', 'balances'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CashBox $cashBox)
    {
        $currencies = Currency::all();
        return view('cash-boxes.edit', compact('cashBox', 'currencies'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CashBox $cashBox)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:cash_boxes,name,' . $cashBox->id,
            'currencies' => 'nullable|array',
            'currencies.*.id' => 'required|exists:currencies,id',
            'currencies.*.balance' => 'required|numeric|min:0',
        ]);

        $cashBox->update($request->only('name', 'location', 'manager'));
        
        $currencyData = [];
        if ($request->has('currencies')) {
            foreach ($request->currencies as $currency) {
                $currencyData[$currency['id']] = ['balance' => $currency['balance']];
            }
        }
        $cashBox->currencies()->sync($currencyData);

        return redirect()->route('cash-boxes.index')
                         ->with('success', __('main.cash_box_updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CashBox $cashBox)
    {
        $cashBox->delete();

        return redirect()->route('cash-boxes.index')
                         ->with('success', __('main.cash_box_deleted_successfully'));
    }
}
