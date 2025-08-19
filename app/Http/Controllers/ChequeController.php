<?php

namespace App\Http\Controllers;

use App\Models\Cheque;
use App\Models\Bank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Currency;

class ChequeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Cheque::with(['bank', 'chequeable']);

        if ($request->filled('search')) {
            $query->where('number', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $cheques = $query->latest()->paginate(10);
        return view('cheques.index', compact('cheques'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
       // Cheques are created via voucher forms, so this is not used.
       return redirect()->route('cheques.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Cheques are created via voucher forms, so this is not used.
        return redirect()->route('cheques.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Cheque $cheque)
    {
        $cheque->load('bank', 'chequeable');
        return view('cheques.show', compact('cheque'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cheque $cheque)
    {
        return view('cheques.edit', compact('cheque'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cheque $cheque)
    {
        $data = $request->validate([
            'status' => 'required|in:pending,cleared,bounced',
        ]);

        DB::transaction(function () use ($data, $cheque) {
            $oldStatus = $cheque->status;
            $newStatus = $data['status'];
            $voucher = $cheque->chequeable;

            if (!$voucher) {
                throw new \Exception('Cheque is not linked to any voucher.');
            }

            $bank = $cheque->bank;
            $currency = Currency::where('code', $voucher->currency)->firstOrFail();

            // Revert old transaction if cheque was cleared
            if ($oldStatus === 'cleared') {
                $bank->currencies()->updateExistingPivot($currency->id, [
                    'balance' => $bank->currencies()->find($currency->id)->pivot->balance - $cheque->amount
                ]);
            }

            $cheque->update($data);

            // Apply new transaction if cheque is now cleared
            if ($newStatus === 'cleared') {
                 $bank->currencies()->updateExistingPivot($currency->id, [
                    'balance' => $bank->currencies()->find($currency->id)->pivot->balance + $cheque->amount
                ]);
            }
        });

        return redirect()->route('cheques.index')
                         ->with('success', __('main.cheque_updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cheque $cheque)
    {
        // Deletion is handled by the parent voucher
        return redirect()->route('cheques.index');
    }
}
