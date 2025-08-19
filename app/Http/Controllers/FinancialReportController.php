<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\ExpenseVoucher;
use App\Models\PaymentVoucher;
use App\Models\Project;
use App\Models\Bank;
use App\Models\CashBox;
use Illuminate\Http\Request;

class FinancialReportController extends Controller
{
    public function index(Request $request)
    {
        $reportType = $request->input('report_type', 'summary');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $accountId = $request->input('account_id');
        $projectId = $request->input('project_id');

        $reportData = null;

        if ($startDate && $endDate) {
            if ($reportType === 'summary') {
                $donations = Donation::whereBetween('date', [$startDate, $endDate])->get();
                $paymentVouchers = PaymentVoucher::whereBetween('date', [$startDate, $endDate])->get();
                $expenseVouchers = ExpenseVoucher::whereBetween('date', [$startDate, $endDate])->get();
                
                $currencyTotals = [];
                
                $allIncome = $donations->concat($paymentVouchers);
                $allExpenses = $expenseVouchers;

                foreach ($allIncome as $item) {
                    $currencyCode = $item->currency;
                    if (!isset($currencyTotals[$currencyCode])) {
                        $this->initializeCurrencyTotals($currencyTotals, $currencyCode);
                    }
                    $currencyTotals[$currencyCode]['total_income'] += $item->amount;
                }
                
                foreach ($allExpenses as $item) {
                    $currencyCode = $item->currency;
                    if (!isset($currencyTotals[$currencyCode])) {
                        $this->initializeCurrencyTotals($currencyTotals, $currencyCode);
                    }
                    $currencyTotals[$currencyCode]['total_expenses'] += $item->amount;
                }

                foreach ($currencyTotals as &$totals) {
                    $totals['net_balance'] = $totals['total_income'] - $totals['total_expenses'];
                }

                $reportData = [
                    'currency_totals' => $currencyTotals,
                    'donations' => $donations,
                    'payment_vouchers' => $paymentVouchers,
                    'expense_vouchers' => $expenseVouchers,
                ];
            } else {
                $query = null;
                if ($reportType === 'payment_vouchers') {
                    $query = PaymentVoucher::query();
                } elseif ($reportType === 'expense_vouchers') {
                    $query = ExpenseVoucher::query();
                }
                
                if ($query) {
                    $query->whereBetween('date', [$startDate, $endDate]);

                    if ($accountId) {
                        list($accountType, $id) = explode('-', $accountId);
                        $query->where('accountable_type', 'App\\Models\\' . $accountType)
                              ->where('accountable_id', $id);
                    }

                    if ($projectId) {
                        $query->where('project_id', $projectId);
                    }

                    $reportData = [
                        'items' => $query->get(),
                    ];
                }
            }

            if ($reportData) {
                $reportData['report_type'] = $reportType;
                $reportData['start_date'] = $startDate;
                $reportData['end_date'] = $endDate;
            }
        }
        
        $projects = Project::all();
        $banks = Bank::all();
        $cashBoxes = CashBox::all();

        return view('financial-reports.index', compact('reportData', 'projects', 'banks', 'cashBoxes'));
    }

    private function initializeCurrencyTotals(&$currencyTotals, $currencyCode)
    {
        $currencyTotals[$currencyCode] = [
            'total_income' => 0,
            'total_expenses' => 0,
            'net_balance' => 0,
            'currency_symbol' => getCurrencyName($currencyCode, true)
        ];
    }
}
