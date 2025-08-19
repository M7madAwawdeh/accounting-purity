<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\Currency;
use Illuminate\Http\Request;

class BankController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Bank::with('currencies');

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('account_number', 'LIKE', "%{$search}%");
        }

        $banks = $query->latest()->paginate(10);
        return view('banks.index', compact('banks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $currencies = Currency::all();
        return view('banks.create', compact('currencies'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:banks,name',
            'account_number' => 'required|string|max:255',
            'iban' => 'nullable|string|max:255',
            'swift_code' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'currencies' => 'required|array|min:1',
            'currencies.*.id' => 'required|exists:currencies,id',
            'currencies.*.balance' => 'required|numeric|min:0',
        ]);

        $bank = Bank::create($request->only('name', 'account_number', 'iban', 'swift_code', 'contact_person', 'phone', 'address'));

        $currencyData = [];
        foreach ($request->currencies as $currency) {
            $currencyData[$currency['id']] = ['balance' => $currency['balance']];
        }
        $bank->currencies()->attach($currencyData);

        return redirect()->route('banks.index')
                         ->with('success', __('main.bank_added_successfully'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Bank $bank)
    {
        $donationsQuery = $bank->donations();
        $paymentVouchersQuery = $bank->paymentVouchers();
        $expenseVouchersQuery = $bank->expenseVouchers();

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

        $balances = $bank->currencies()->get()->mapWithKeys(function ($currency) {
            return [$currency->code => $currency->pivot->balance];
        });

        return view('banks.show', compact('bank', 'transactions', 'balances'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bank $bank)
    {
        $currencies = Currency::all();
        return view('banks.edit', compact('bank', 'currencies'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Bank $bank)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:banks,name,' . $bank->id,
            'account_number' => 'required|string|max:255',
            'iban' => 'nullable|string|max:255',
            'swift_code' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'currencies' => 'nullable|array',
            'currencies.*.id' => 'required|exists:currencies,id',
            'currencies.*.balance' => 'required|numeric|min:0',
        ]);

        $bank->update($request->only('name', 'account_number', 'iban', 'swift_code', 'contact_person', 'phone', 'address'));
        
        $currencyData = [];
        if ($request->has('currencies')) {
            foreach ($request->currencies as $currency) {
                $currencyData[$currency['id']] = ['balance' => $currency['balance']];
            }
        }
        $bank->currencies()->sync($currencyData);

        return redirect()->route('banks.index')
                         ->with('success', __('main.bank_updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bank $bank)
    {
        $bank->delete();

        return redirect()->route('banks.index')
                         ->with('success', __('main.bank_deleted_successfully'));
    }
}
