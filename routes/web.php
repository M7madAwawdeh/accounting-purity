<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\ExpenseVoucherController;
use App\Http\Controllers\PaymentVoucherController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\FunderController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectPaymentController;
use App\Http\Controllers\ProjectExpenseController;
use App\Http\Controllers\FinancialReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CurrencyController;

Route::get('/', [LoginController::class, 'showLoginForm']);

Auth::routes(['register' => false, 'reset' => false, 'verify' => false]);

Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return redirect()->route('home');
    });
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    // Financial Reports
    Route::get('financial-reports', [\App\Http\Controllers\FinancialReportController::class, 'index'])->name('financial-reports.index');

    Route::resource('donations', DonationController::class);
    Route::resource('expense-vouchers', ExpenseVoucherController::class);
    Route::resource('payment-vouchers', PaymentVoucherController::class);
    Route::resource('employees', EmployeeController::class);
    Route::resource('banks', BankController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::resource('members', MemberController::class);
    Route::resource('funders', FunderController::class);
    Route::resource('projects', ProjectController::class);
    Route::resource('currencies', CurrencyController::class);

    Route::resource('cash-boxes', \App\Http\Controllers\CashBoxController::class);
    Route::resource('cheques', \App\Http\Controllers\ChequeController::class);

    Route::get('account-transfers', [\App\Http\Controllers\AccountTransferController::class, 'create'])->name('account-transfers.create');
    Route::post('account-transfers', [\App\Http\Controllers\AccountTransferController::class, 'store'])->name('account-transfers.store');

    Route::get('account-report', [\App\Http\Controllers\AccountReportController::class, 'index'])->name('account-report.index');
    Route::post('account-report', [\App\Http\Controllers\AccountReportController::class, 'generate'])->name('account-report.generate');

    Route::prefix('projects/{project}')->name('projects.')->group(function () {
        Route::resource('payments', ProjectPaymentController::class);
        Route::resource('expenses', ProjectExpenseController::class);
    });
    
    Route::get('/settings', [App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [App\Http\Controllers\SettingController::class, 'store'])->name('settings.store');
    Route::post('/settings/backup', [App\Http\Controllers\SettingController::class, 'createBackup'])->name('settings.backup');
    Route::get('/settings/backup/{filename}', [App\Http\Controllers\SettingController::class, 'downloadBackup'])->name('settings.download-backup');
    Route::get('/settings/backups', [App\Http\Controllers\SettingController::class, 'listBackups'])->name('settings.list-backups');
    Route::delete('/settings/backup/{filename}', [App\Http\Controllers\SettingController::class, 'deleteBackup'])->name('settings.delete-backup');
    Route::post('/settings/backup/{filename}/restore', [App\Http\Controllers\SettingController::class, 'restoreBackup'])->name('settings.restore-backup');
    Route::post('/settings/upload-backup', [App\Http\Controllers\SettingController::class, 'uploadBackup'])->name('settings.upload-backup');
    Route::resource('settings', App\Http\Controllers\SettingController::class)->only(['index', 'store']);
    Route::resource('currencies', App\Http\Controllers\CurrencyController::class);
    Route::get('/api/account-details/{type}/{id}', [App\Http\Controllers\AccountTransferController::class, 'getAccountDetails'])->name('api.account_details');
});
