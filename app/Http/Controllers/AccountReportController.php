<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bank;
use App\Models\CashBox;
use App\Models\Currency;

class AccountReportController extends Controller
{
    public function index()
    {
        $banks = Bank::all();
        $cashBoxes = CashBox::all();
        $currencies = Currency::all();
        return view('account-report.index', compact('banks', 'cashBoxes', 'currencies'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'account' => 'required|string',
            'currency_id' => 'required',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
        ]);

        list($account_type, $account_id) = explode('-', $request->account);
        $account_type = 'App\\Models\\' . ucfirst($account_type);
        $account = $account_type::findOrFail($account_id);
        
        $selected_currency = null;
        if ($request->currency_id !== 'all') {
            $selected_currency = Currency::findOrFail($request->currency_id);
        }

        $donationsQuery = $account->donations()->select('date', 'amount', 'currency', 'donor_name as details', \DB::raw('"donation" as type'));
        $paymentsQuery = $account->paymentVouchers()->select('date', 'amount', 'currency', 'payer as details', \DB::raw('"payment_voucher" as type'));
        $expensesQuery = $account->expenseVouchers()->select('date', \DB::raw('-amount as amount'), 'currency', 'recipient as details', \DB::raw('"expense_voucher" as type'));

        if ($selected_currency) {
            $donationsQuery->where('currency', $selected_currency->code);
            $paymentsQuery->where('currency', $selected_currency->code);
            $expensesQuery->where('currency', $selected_currency->code);
        }
        
        $query = $donationsQuery->unionAll($paymentsQuery)->unionAll($expensesQuery);

        if ($request->filled('from_date')) {
            $query->where('date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->where('date', '<=', $request->to_date);
        }

        $transactions = $query->orderBy('date')->get();

        $banks = Bank::all();
        $cashBoxes = CashBox::all();
        $currencies = Currency::all();

        return view('account-report.index', [
            'banks' => $banks,
            'cashBoxes' => $cashBoxes,
            'currencies' => $currencies,
            'transactions' => $transactions,
            'account' => $account,
            'selected_currency' => $selected_currency,
            'inputs' => $request->all(),
        ]);
    }
}
