<?php

namespace App\Http\Controllers;

use App\Models\PaymentVoucher;
use App\Models\Project;
use App\Models\Bank;
use App\Models\CashBox;
use App\Models\Cheque;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Currency;

class PaymentVoucherController extends Controller
{
    public function index(Request $request)
    {
        $query = PaymentVoucher::with('project');

        if ($request->filled('search')) {
            $query->where('payer', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        $paymentVouchers = $query->latest()->paginate(10);
        $projects = Project::all();
        
        return view('payment-vouchers.index', compact('paymentVouchers', 'projects'));
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
        return view('payment-vouchers.create', compact('projects', 'accounts', 'defaultProject', 'currencies'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'payer' => 'required|string|max:255',
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

            $voucher = new PaymentVoucher($data);
            $voucher->accountable()->associate($account);
            $voucher->currency = $currency->code;
            $voucher->save();

            if (isset($data['project_id']) && $data['project_id'] != 0) {
                $project = Project::find($data['project_id']);
                if ($project) {
                    if (!$project->currencies->contains($currency->id)) {
                        $project->currencies()->attach($currency->id);
                    }
                    $netAmount = $data['amount'] - ($data['association_fee'] ?? 0);
                    $project->currencies()->updateExistingPivot($currency->id, [
                        'balance' => $project->currencies()->find($currency->id)->pivot->balance + $netAmount,
                        'total_payments' => $project->currencies()->find($currency->id)->pivot->total_payments + $data['amount'],
                    ]);

                    // Update project's main value
                    $projectCurrency = Currency::where('code', $project->currency)->firstOrFail();
                    $amountInProjectCurrency = $netAmount * ($projectCurrency->exchange_rate / $currency->exchange_rate);
                    $project->value += $amountInProjectCurrency;
                    $project->save();
                }
            }

            if ($data['payment_method'] !== 'cheque') {
                $account->currencies()->updateExistingPivot($currency->id, [
                    'balance' => $account->currencies()->find($currency->id)->pivot->balance + $data['amount']
                ]);
            } else {
                $voucher->cheque()->create([
                    'bank_id' => $account->id,
                    'number' => $data['number'],
                    'account_number' => $data['cheque_account_number'],
                    'amount' => $data['amount'],
                    'due_date' => $data['cheque_due_date'],
                    'type' => 'incoming',
                    'status' => 'pending',
                ]);
            }

            DB::commit();

            return redirect()->route('payment-vouchers.index')->with('success', __('main.payment_voucher_added_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', __('main.error_adding_payment_voucher') . $e->getMessage())->withInput();
        }
    }

    public function show(Request $request, PaymentVoucher $paymentVoucher)
    {
        $print = $request->query('print', false);
        return view('payment-vouchers.show', compact('paymentVoucher', 'print'));
    }

    public function edit(PaymentVoucher $paymentVoucher)
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
        return view('payment-vouchers.edit', compact('paymentVoucher', 'projects', 'accounts', 'defaultProject', 'currencies'));
    }

    public function update(Request $request, PaymentVoucher $paymentVoucher)
    {
        $data = $request->validate([
            'payer' => 'required|string|max:255',
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

            $oldAccount = $paymentVoucher->accountable;
            $oldCurrency = Currency::where('code', $paymentVoucher->currency)->firstOrFail();
            $newAccount = $data['accountable_type']::findOrFail($data['accountable_id']);
            $newCurrency = Currency::where('code', $data['currency'])->firstOrFail();

            if ($oldAccount && $paymentVoucher->payment_method !== 'cheque') {
                $oldAccount->currencies()->updateExistingPivot($oldCurrency->id, [
                    'balance' => $oldAccount->currencies()->find($oldCurrency->id)->pivot->balance - $paymentVoucher->amount
                ]);
            }
            if ($paymentVoucher->project) {
                $oldNetAmount = $paymentVoucher->amount - $paymentVoucher->association_fee;
                $paymentVoucher->project->currencies()->updateExistingPivot($oldCurrency->id, [
                    'balance' => $paymentVoucher->project->currencies()->find($oldCurrency->id)->pivot->balance - $oldNetAmount,
                    'total_payments' => $paymentVoucher->project->currencies()->find($oldCurrency->id)->pivot->total_payments - $paymentVoucher->amount,
                ]);

                // Revert project's main value
                $projectCurrency = Currency::where('code', $paymentVoucher->project->currency)->firstOrFail();
                $amountInProjectCurrency = $oldNetAmount * ($projectCurrency->exchange_rate / $oldCurrency->exchange_rate);
                $paymentVoucher->project->value -= $amountInProjectCurrency;
                $paymentVoucher->project->save();
            }

            $paymentVoucher->fill($data);
            $paymentVoucher->accountable()->associate($newAccount);
            $paymentVoucher->currency = $newCurrency->code;
            $paymentVoucher->save();

            if (!$newAccount->currencies->contains($newCurrency->id)) {
                $newAccount->currencies()->attach($newCurrency->id, ['balance' => 0]);
                $newAccount->load('currencies');
            }

            if ($data['payment_method'] !== 'cheque') {
                $newAccount->currencies()->updateExistingPivot($newCurrency->id, [
                    'balance' => $newAccount->currencies()->find($newCurrency->id)->pivot->balance + $data['amount']
                ]);
            }

            if ($paymentVoucher->project) {
                if (!$paymentVoucher->project->currencies->contains($newCurrency->id)) {
                    $paymentVoucher->project->currencies()->attach($newCurrency->id);
                }
                $newNetAmount = $data['amount'] - ($data['association_fee'] ?? 0);
                $paymentVoucher->project->currencies()->updateExistingPivot($newCurrency->id, [
                    'balance' => $paymentVoucher->project->currencies()->find($newCurrency->id)->pivot->balance + $newNetAmount,
                    'total_payments' => $paymentVoucher->project->currencies()->find($newCurrency->id)->pivot->total_payments + $data['amount'],
                ]);

                // Update project's main value
                $projectCurrency = Currency::where('code', $paymentVoucher->project->currency)->firstOrFail();
                $amountInProjectCurrency = $newNetAmount * ($projectCurrency->exchange_rate / $newCurrency->exchange_rate);
                $paymentVoucher->project->value += $amountInProjectCurrency;
                $paymentVoucher->project->save();
            }

            if ($data['payment_method'] === 'cheque') {
                $chequeData = [
                    'bank_id' => $newAccount->id,
                    'number' => $data['number'],
                    'account_number' => $data['cheque_account_number'],
                    'amount' => $data['amount'],
                    'due_date' => $data['cheque_due_date'],
                ];
                if ($paymentVoucher->cheque) {
                    $paymentVoucher->cheque->update($chequeData);
                } else {
                    $paymentVoucher->cheque()->create($chequeData + ['type' => 'incoming', 'status' => 'pending']);
                }
            } elseif ($paymentVoucher->cheque) {
                $paymentVoucher->cheque->delete();
            }

            DB::commit();
            return redirect()->route('payment-vouchers.index')->with('success', __('main.payment_voucher_updated_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', __('main.error_updating_payment_voucher') . $e->getMessage())->withInput();
        }
    }

    public function destroy(PaymentVoucher $paymentVoucher)
    {
        try {
            DB::beginTransaction();

            $currency = Currency::where('code', $paymentVoucher->currency)->firstOrFail();

            if ($paymentVoucher->payment_method !== 'cheque') {
                $account = $paymentVoucher->accountable;
                if ($account) {
                    $account->currencies()->updateExistingPivot($currency->id, [
                        'balance' => $account->currencies()->find($currency->id)->pivot->balance - $paymentVoucher->amount
                    ]);
                }
            } elseif ($paymentVoucher->cheque) {
                $paymentVoucher->cheque->delete();
            }

            if ($paymentVoucher->project) {
                $netAmount = $paymentVoucher->amount - $paymentVoucher->association_fee;
                $paymentVoucher->project->currencies()->updateExistingPivot($currency->id, [
                    'balance' => $paymentVoucher->project->currencies()->find($currency->id)->pivot->balance - $netAmount,
                    'total_payments' => $paymentVoucher->project->currencies()->find($currency->id)->pivot->total_payments - $paymentVoucher->amount,
                ]);

                // Revert project's main value
                $projectCurrency = Currency::where('code', $paymentVoucher->project->currency)->firstOrFail();
                $amountInProjectCurrency = $netAmount * ($projectCurrency->exchange_rate / $currency->exchange_rate);
                $paymentVoucher->project->value -= $amountInProjectCurrency;
                $paymentVoucher->project->save();
            }

            $paymentVoucher->delete();

            DB::commit();
            return redirect()->route('payment-vouchers.index')->with('success', __('main.payment_voucher_deleted_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', __('main.error_deleting_payment_voucher') . $e->getMessage());
        }
    }
}
