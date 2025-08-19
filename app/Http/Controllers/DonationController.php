<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\Project;
use App\Models\Bank;
use App\Models\CashBox;
use App\Models\Cheque;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Currency;

class DonationController extends Controller
{
    public function index(Request $request)
    {
        $query = Donation::with('project');

        if ($request->filled('search')) {
            $query->where('donor_name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }
        
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        $donations = $query->latest()->paginate(10);
        $projects = Project::all();

        return view('donations.index', compact('donations', 'projects'));
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
        return view('donations.create', compact('projects', 'accounts', 'defaultProject', 'currencies'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'donor_name' => 'required|string|max:255',
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

            $donation = new Donation($data);
            $donation->accountable()->associate($account);
            $donation->currency = $currency->code;
            $donation->save();

            if (isset($data['project_id']) && $data['project_id'] != 0) {
                $project = Project::find($data['project_id']);
                if ($project) {
                    if (!$project->currencies->contains($currency->id)) {
                        $project->currencies()->attach($currency->id);
                    }
                    $netAmount = $data['amount'] - ($data['association_fee'] ?? 0);
                    $project->currencies()->updateExistingPivot($currency->id, [
                        'balance' => $project->currencies()->find($currency->id)->pivot->balance + $netAmount,
                        'total_donations' => $project->currencies()->find($currency->id)->pivot->total_donations + $data['amount'],
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
                $donation->cheque()->create([
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

            return redirect()->route('donations.index')->with('success', __('main.donation_added_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', __('main.error_adding_donation') . $e->getMessage())->withInput();
        }
    }

    public function show(Request $request, Donation $donation)
    {
        $print = $request->query('print', false);
        return view('donations.show', compact('donation', 'print'));
    }

    public function edit(Donation $donation)
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
        return view('donations.edit', compact('donation', 'projects', 'accounts', 'defaultProject', 'currencies'));
    }

    public function update(Request $request, Donation $donation)
    {
        $data = $request->validate([
            'donor_name' => 'required|string|max:255',
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

            $oldAccount = $donation->accountable;
            $oldCurrency = Currency::where('code', $donation->currency)->firstOrFail();
            $newAccount = $data['accountable_type']::findOrFail($data['accountable_id']);
            $newCurrency = Currency::where('code', $data['currency'])->firstOrFail();

            if ($oldAccount && $donation->payment_method !== 'cheque') {
                $oldAccount->currencies()->updateExistingPivot($oldCurrency->id, [
                    'balance' => $oldAccount->currencies()->find($oldCurrency->id)->pivot->balance - $donation->amount
                ]);
            }
            if ($donation->project) {
                $oldNetAmount = $donation->amount - $donation->association_fee;
                $donation->project->currencies()->updateExistingPivot($oldCurrency->id, [
                    'balance' => $donation->project->currencies()->find($oldCurrency->id)->pivot->balance - $oldNetAmount,
                    'total_donations' => $donation->project->currencies()->find($oldCurrency->id)->pivot->total_donations - $donation->amount,
                ]);

                // Revert project's main value
                $projectCurrency = Currency::where('code', $donation->project->currency)->firstOrFail();
                $amountInProjectCurrency = $oldNetAmount * ($projectCurrency->exchange_rate / $oldCurrency->exchange_rate);
                $donation->project->value -= $amountInProjectCurrency;
                $donation->project->save();
            }
            
            $donation->fill($data);
            $donation->accountable()->associate($newAccount);
            $donation->currency = $newCurrency->code;
            $donation->save();

            if (!$newAccount->currencies->contains($newCurrency->id)) {
                $newAccount->currencies()->attach($newCurrency->id, ['balance' => 0]);
                $newAccount->load('currencies');
            }

            if ($data['payment_method'] !== 'cheque') {
                $newAccount->currencies()->updateExistingPivot($newCurrency->id, [
                    'balance' => $newAccount->currencies()->find($newCurrency->id)->pivot->balance + $data['amount']
                ]);
            }
            
            if ($donation->project) {
                if (!$donation->project->currencies->contains($newCurrency->id)) {
                    $donation->project->currencies()->attach($newCurrency->id);
                }
                $newNetAmount = $data['amount'] - ($data['association_fee'] ?? 0);
                $donation->project->currencies()->updateExistingPivot($newCurrency->id, [
                    'balance' => $donation->project->currencies()->find($newCurrency->id)->pivot->balance + $newNetAmount,
                    'total_donations' => $donation->project->currencies()->find($newCurrency->id)->pivot->total_donations + $data['amount'],
                ]);

                // Update project's main value
                $projectCurrency = Currency::where('code', $donation->project->currency)->firstOrFail();
                $amountInProjectCurrency = $newNetAmount * ($projectCurrency->exchange_rate / $newCurrency->exchange_rate);
                $donation->project->value += $amountInProjectCurrency;
                $donation->project->save();
            }
            
            if ($data['payment_method'] === 'cheque') {
                $chequeData = [
                    'bank_id' => $newAccount->id,
                    'number' => $data['number'],
                    'account_number' => $data['cheque_account_number'],
                    'amount' => $data['amount'],
                    'due_date' => $data['cheque_due_date'],
                ];
                if ($donation->cheque) {
                    $donation->cheque->update($chequeData);
                } else {
                    $donation->cheque()->create($chequeData + ['type' => 'incoming', 'status' => 'pending']);
                }
            } elseif ($donation->cheque) {
                $donation->cheque->delete();
            }

            DB::commit();
            return redirect()->route('donations.index')->with('success', __('main.donation_updated_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', __('main.error_updating_donation') . $e->getMessage())->withInput();
        }
    }

    public function destroy(Donation $donation)
    {
        try {
            DB::beginTransaction();
            
            $currency = Currency::where('code', $donation->currency)->firstOrFail();

            if ($donation->payment_method !== 'cheque') {
                $account = $donation->accountable;
                if ($account) {
                    $account->currencies()->updateExistingPivot($currency->id, [
                        'balance' => $account->currencies()->find($currency->id)->pivot->balance - $donation->amount
                    ]);
                }
            } elseif ($donation->cheque) {
                $donation->cheque->delete();
            }
            
            // Update project collected amount
            if ($donation->project_id && $donation->project_id != 0) {
                $project = Project::find($donation->project_id);
                $netAmount = $donation->amount - $donation->association_fee;
                $project->currencies()->updateExistingPivot($currency->id, [
                    'balance' => $project->currencies()->find($currency->id)->pivot->balance - $netAmount,
                    'total_donations' => $project->currencies()->find($currency->id)->pivot->total_donations - $donation->amount,
                ]);

                // Revert project's main value
                $projectCurrency = Currency::where('code', $project->currency)->firstOrFail();
                $amountInProjectCurrency = $netAmount * ($projectCurrency->exchange_rate / $currency->exchange_rate);
                $project->value -= $amountInProjectCurrency;
                $project->save();
            }

            $donation->delete();

            DB::commit();
            return redirect()->route('donations.index')->with('success', __('main.donation_deleted_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', __('main.error_deleting_donation') . $e->getMessage());
        }
    }
}
