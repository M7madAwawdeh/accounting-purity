<?php

namespace App\Http\Controllers;

use App\Models\ExpenseVoucher;
use App\Models\Project;
use App\Models\Bank;
use App\Models\CashBox;
use App\Models\Cheque;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Currency;

class ExpenseVoucherController extends Controller
{
    public function index(Request $request)
    {
        $query = ExpenseVoucher::with('project');

        if ($request->filled('search')) {
            $query->where('recipient', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        $expenseVouchers = $query->latest()->paginate(10);
        $projects = Project::all();
        
        return view('expense-vouchers.index', compact('expenseVouchers', 'projects'));
    }

    public function create()
    {
        $projects = Project::all();
        $banks = Bank::all();
        $cashBoxes = CashBox::all();
        $defaultProject = Project::where('is_default', true)->first();
        $currencies = Currency::all();
        $accounts = [
            'cash' => $cashBoxes->map(fn($box) => ['id' => $box->id, 'name' => $box->name, 'type' => get_class($box)]),
            'bank' => $banks->map(fn($bank) => ['id' => $bank->id, 'name' => $bank->name, 'type' => get_class($bank), 'account_number' => $bank->account_number]),
        ];
        return view('expense-vouchers.create', compact('projects', 'accounts', 'defaultProject', 'currencies'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'recipient' => 'required|string|max:255',
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|string|exists:currencies,code',
            'description' => 'nullable|string',
            'project_id' => 'nullable|exists:projects,id',
            'payment_method' => 'required|in:cash,bank_transfer,cheque',
            'accountable_type' => 'required|string',
            'accountable_id' => 'required|integer',
            'number' => 'required_if:payment_method,cheque|nullable|string|max:255',
            'cheque_account_number' => 'required_if:payment_method,cheque|nullable|string|max:255',
            'cheque_due_date' => 'required_if:payment_method,cheque|nullable|date',
            'association_fee' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $account = $data['accountable_type']::findOrFail($data['accountable_id']);
            $currency = Currency::where('code', $data['currency'])->firstOrFail();

            if (!$account->currencies->contains($currency->id)) {
                $account->currencies()->attach($currency->id, ['balance' => 0]);
                $account->load('currencies');
            }
            
            if ($account->currencies()->find($currency->id)->pivot->balance < $data['amount'] && $data['payment_method'] !== 'cheque') {
                 return redirect()->back()->withErrors(['amount' => __('main.insufficient_balance')]);
            }
            
            $voucher = new ExpenseVoucher($data);
            $voucher->accountable()->associate($account);
            $voucher->currency = $currency->code;
            $voucher->save();

            if (isset($data['project_id']) && $data['project_id'] != 0) {
                $project = Project::find($data['project_id']);
                if ($project) {
                    if (!$project->currencies->contains($currency->id)) {
                        $project->currencies()->attach($currency->id);
                    }
                    $netAmount = $data['amount'] + ($data['association_fee'] ?? 0);
                    $project->currencies()->updateExistingPivot($currency->id, [
                        'balance' => $project->currencies()->find($currency->id)->pivot->balance - $netAmount,
                        'total_expenses' => $project->currencies()->find($currency->id)->pivot->total_expenses + $data['amount'],
                    ]);
                }
            }

            if ($data['payment_method'] !== 'cheque') {
                $account->currencies()->updateExistingPivot($currency->id, [
                    'balance' => $account->currencies()->find($currency->id)->pivot->balance - $data['amount']
                ]);
            } else {
                $voucher->cheque()->create([
                    'bank_id' => $account->id,
                    'number' => $data['number'],
                    'account_number' => $data['cheque_account_number'],
                    'amount' => $data['amount'],
                    'due_date' => $data['cheque_due_date'],
                    'type' => 'outgoing',
                    'status' => 'pending',
                ]);
            }
            
            DB::commit();

            return redirect()->route('expense-vouchers.index')->with('success', __('main.expense_voucher_added_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', __('main.error_adding_expense_voucher') . $e->getMessage())->withInput();
        }
    }

    public function show(Request $request, ExpenseVoucher $expenseVoucher)
    {
        $print = $request->query('print', false);
        return view('expense-vouchers.show', compact('expenseVoucher', 'print'));
    }

    public function edit(ExpenseVoucher $expenseVoucher)
    {
        $projects = Project::all();
        $banks = Bank::all();
        $cashBoxes = CashBox::all();
        $defaultProject = Project::where('is_default', true)->first();
        $currencies = Currency::all();
        $accounts = [
            'cash' => $cashBoxes->map(fn($box) => ['id' => $box->id, 'name' => $box->name, 'type' => get_class($box)]),
            'bank' => $banks->map(fn($bank) => ['id' => $bank->id, 'name' => $bank->name, 'type' => get_class($bank), 'account_number' => $bank->account_number]),
        ];
        return view('expense-vouchers.edit', compact('expenseVoucher', 'projects', 'accounts', 'defaultProject', 'currencies'));
    }

    public function update(Request $request, ExpenseVoucher $expenseVoucher)
    {
        $data = $request->validate([
            'recipient' => 'required|string|max:255',
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|string|exists:currencies,code',
            'description' => 'nullable|string',
            'project_id' => 'nullable|exists:projects,id',
            'payment_method' => 'required|in:cash,bank_transfer,cheque',
            'accountable_type' => 'required|string',
            'accountable_id' => 'required|integer',
            'number' => 'required_if:payment_method,cheque|nullable|string|max:255',
            'cheque_account_number' => 'required_if:payment_method,cheque|nullable|string|max:255',
            'cheque_due_date' => 'required_if:payment_method,cheque|nullable|date',
            'association_fee' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $oldAccount = $expenseVoucher->accountable;
            $oldCurrency = Currency::where('code', $expenseVoucher->currency)->firstOrFail();
            $newAccount = $data['accountable_type']::findOrFail($data['accountable_id']);
            $newCurrency = Currency::where('code', $data['currency'])->firstOrFail();

            if ($oldAccount && $expenseVoucher->payment_method !== 'cheque') {
                $oldAccount->currencies()->updateExistingPivot($oldCurrency->id, [
                    'balance' => $oldAccount->currencies()->find($oldCurrency->id)->pivot->balance + $expenseVoucher->amount
                ]);
            }
            if ($expenseVoucher->project) {
                $oldNetAmount = $expenseVoucher->amount + $expenseVoucher->association_fee;
                $expenseVoucher->project->currencies()->updateExistingPivot($oldCurrency->id, [
                    'balance' => $expenseVoucher->project->currencies()->find($oldCurrency->id)->pivot->balance + $oldNetAmount,
                    'total_expenses' => $expenseVoucher->project->currencies()->find($oldCurrency->id)->pivot->total_expenses - $expenseVoucher->amount,
                ]);
            }

            $expenseVoucher->fill($data);
            $expenseVoucher->accountable()->associate($newAccount);
            $expenseVoucher->currency = $newCurrency->code;
            $expenseVoucher->save();

            if (!$newAccount->currencies->contains($newCurrency->id)) {
                $newAccount->currencies()->attach($newCurrency->id, ['balance' => 0]);
                $newAccount->load('currencies');
            }

            if ($data['payment_method'] !== 'cheque') {
                 if ($newAccount->currencies()->find($newCurrency->id)->pivot->balance < $data['amount']) {
                    return redirect()->back()->withErrors(['amount' => __('main.insufficient_balance')]);
                }
                $newAccount->currencies()->updateExistingPivot($newCurrency->id, [
                    'balance' => $newAccount->currencies()->find($newCurrency->id)->pivot->balance - $data['amount']
                ]);
            }

            if ($expenseVoucher->project) {
                if (!$expenseVoucher->project->currencies->contains($newCurrency->id)) {
                    $expenseVoucher->project->currencies()->attach($newCurrency->id);
                }
                $newNetAmount = $data['amount'] + ($data['association_fee'] ?? 0);
                $expenseVoucher->project->currencies()->updateExistingPivot($newCurrency->id, [
                    'balance' => $expenseVoucher->project->currencies()->find($newCurrency->id)->pivot->balance - $newNetAmount,
                    'total_expenses' => $expenseVoucher->project->currencies()->find($newCurrency->id)->pivot->total_expenses + $data['amount'],
                ]);
            }

            if ($data['payment_method'] === 'cheque') {
                $chequeData = [
                    'bank_id' => $newAccount->id,
                    'number' => $data['number'],
                    'account_number' => $data['cheque_account_number'],
                    'amount' => $data['amount'],
                    'due_date' => $data['cheque_due_date'],
                ];
                if ($expenseVoucher->cheque) {
                    $expenseVoucher->cheque->update($chequeData);
                } else {
                    $expenseVoucher->cheque()->create($chequeData + ['type' => 'outgoing', 'status' => 'pending']);
                }
            } elseif ($expenseVoucher->cheque) {
                $expenseVoucher->cheque->delete();
            }

            DB::commit();
            return redirect()->route('expense-vouchers.index')->with('success', __('main.expense_voucher_updated_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', __('main.error_updating_expense_voucher') . $e->getMessage())->withInput();
        }
    }

    public function destroy(ExpenseVoucher $expenseVoucher)
    {
        try {
            DB::beginTransaction();

            $currency = Currency::where('code', $expenseVoucher->currency)->firstOrFail();

            if ($expenseVoucher->payment_method !== 'cheque') {
                $account = $expenseVoucher->accountable;
                if ($account) {
                    $account->currencies()->updateExistingPivot($currency->id, [
                        'balance' => $account->currencies()->find($currency->id)->pivot->balance + $expenseVoucher->amount
                    ]);
                }
            } elseif ($expenseVoucher->cheque) {
                $expenseVoucher->cheque->delete();
            }

            if ($expenseVoucher->project) {
                $netAmount = $expenseVoucher->amount + $expenseVoucher->association_fee;
                $expenseVoucher->project->currencies()->updateExistingPivot($currency->id, [
                    'balance' => $expenseVoucher->project->currencies()->find($currency->id)->pivot->balance + $netAmount,
                    'total_expenses' => $expenseVoucher->project->currencies()->find($currency->id)->pivot->total_expenses - $expenseVoucher->amount,
                ]);
            }

            $expenseVoucher->delete();

            DB::commit();
            return redirect()->route('expense-vouchers.index')->with('success', __('main.expense_voucher_deleted_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', __('main.error_deleting_expense_voucher') . $e->getMessage());
        }
    }
}
