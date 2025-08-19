<?php

namespace App\Http\Controllers;

use App\Models\AccountTransfer;
use App\Models\Bank;
use App\Models\CashBox;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountTransferController extends Controller
{
    /**
     * Show the form for creating a new transfer
     */
    public function create()
    {
        $banks = Bank::with('currencies')->get();
        $cashBoxes = CashBox::with('currencies')->get();

        $accounts = $banks->map(function ($account) {
            return $this->formatAccountData($account, 'b');
        })->concat($cashBoxes->map(function ($account) {
            return $this->formatAccountData($account, 'c');
        }));

        $banksFormatted = $accounts->where('type_prefix', 'b')->values();
        $cashBoxesFormatted = $accounts->where('type_prefix', 'c')->values();

        // Fetch all currencies for the dropdown
        $allCurrencies = Currency::all();

        return view('account-transfers.create', compact('accounts', 'banksFormatted', 'cashBoxesFormatted', 'allCurrencies'));
    }

    private function formatAccountData($account, $prefix)
    {
        return [
            'id' => $prefix . '_' . $account->id,
            'name' => $account->name,
            'type_prefix' => $prefix,
            'currencies' => $account->currencies->map(function ($currency) {
                return [
                    'id' => $currency->id,
                    'name' => $currency->name,
                    'code' => $currency->code,
                    'balance' => $currency->pivot->balance ?? 0,
                ];
            })->keyBy('id'),
        ];
    }

    /**
     * Store a new transfer
     */
    public function store(Request $request)
    {
        $request->validate([
            'from_account_id' => 'required|string',
            'to_account_id' => 'required|string|different:from_account_id',
            'currency_id' => 'required|exists:currencies,id',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $amount = (float)$request->amount;
                $currencyId = (int)$request->currency_id;

                // From Account
                $fromAccountModel = $this->getAccountModel($request->from_account_id);
                $fromBalance = $fromAccountModel->currencies()->where('currency_id', $currencyId)->first()->pivot->balance;
                if ($fromBalance < $amount) {
                    throw new \Exception(__('main.insufficient_balance'));
                }
                $fromAccountModel->currencies()->updateExistingPivot($currencyId, ['balance' => $fromBalance - $amount]);

                // To Account
                $toAccountModel = $this->getAccountModel($request->to_account_id);
                $toBalancePivot = $toAccountModel->currencies()->where('currency_id', $currencyId)->first();
                if ($toBalancePivot) {
                    $toAccountModel->currencies()->updateExistingPivot($currencyId, ['balance' => $toBalancePivot->pivot->balance + $amount]);
                } else {
                    $toAccountModel->currencies()->attach($currencyId, ['balance' => $amount]);
                }

                // Record the transfer
                AccountTransfer::create([
                    'from_account_type' => $this->getAccountType($request->from_account_id),
                    'from_account_id' => $this->getAccountId($request->from_account_id),
                    'to_account_type' => $this->getAccountType($request->to_account_id),
                    'to_account_id' => $this->getAccountId($request->to_account_id),
                    'currency_id' => $currencyId,
                    'amount' => $amount,
                    'date' => $request->date,
                    'notes' => $request->notes,
                ]);
            });
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }

        return redirect()->route('account-transfers.create')->with('success', __('main.transfer_successful'));
    }

    private function getAccountType(string $fullId): string
    {
        return str_starts_with($fullId, 'b_') ? 'bank' : 'cash_box';
    }

    private function getAccountId(string $fullId): int
    {
        return (int) substr($fullId, 2);
    }

    private function getAccountModel(string $fullId)
    {
        $type = $this->getAccountType($fullId);
        $id = $this->getAccountId($fullId);
        $modelClass = $type === 'bank' ? Bank::class : CashBox::class;
        return $modelClass::lockForUpdate()->findOrFail($id);
    }
}
