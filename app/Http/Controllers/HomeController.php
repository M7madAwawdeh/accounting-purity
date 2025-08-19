<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Donation;
use App\Models\ExpenseVoucher;
use App\Models\PaymentVoucher;
use App\Models\Project;
use App\Models\Funder;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $donations = Donation::all();
        $paymentVouchers = PaymentVoucher::all();
        $expenseVouchers = ExpenseVoucher::all();

        $currencyTotals = [];
        $allIncome = $donations->concat($paymentVouchers);

        foreach ($allIncome as $item) {
            if (!isset($currencyTotals[$item->currency])) {
                $currencyTotals[$item->currency] = ['income' => 0, 'expenses' => 0, 'net' => 0, 'symbol' => getCurrencyName($item->currency, true)];
            }
            $currencyTotals[$item->currency]['income'] += $item->amount;
        }

        foreach ($expenseVouchers as $item) {
             if (!isset($currencyTotals[$item->currency])) {
                $currencyTotals[$item->currency] = ['income' => 0, 'expenses' => 0, 'net' => 0, 'symbol' => getCurrencyName($item->currency, true)];
            }
            $currencyTotals[$item->currency]['expenses'] += $item->amount;
        }

        foreach ($currencyTotals as &$totals) {
            $totals['net'] = $totals['income'] - $totals['expenses'];
        }
        
        $stats = [
            'projects_count' => Project::count(),
            'funders_count' => Funder::count(),
        ];
        
        $recentDonations = Donation::latest()->take(5)->get();
        $recentExpenses = ExpenseVoucher::latest()->take(5)->get();
        $recentPaymentVouchers = PaymentVoucher::latest()->take(5)->get();

        // Data for chart
        $monthlyData = $this->getMonthlyFinancialData();

        return view('home', compact('stats', 'recentDonations', 'recentExpenses', 'recentPaymentVouchers', 'monthlyData', 'currencyTotals'));
    }

    private function getMonthlyFinancialData()
    {
        $months = [];
        $incomeData = [];
        $expenseData = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthName = $date->format('F');
            $months[] = $monthName;

            $income = Donation::whereYear('date', $date->year)->whereMonth('date', $date->month)->sum('amount')
                      + PaymentVoucher::whereYear('date', $date->year)->whereMonth('date', $date->month)->sum('amount');
            $incomeData[] = $income;

            $expenses = ExpenseVoucher::whereYear('date', $date->year)->whereMonth('date', $date->month)->sum('amount');
            $expenseData[] = $expenses;
        }

        return [
            'months' => $months,
            'income' => $incomeData,
            'expenses' => $expenseData,
        ];
    }
}
