<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    public function index()
    {
        $currencies = Currency::all();
        return view('currencies.index', compact('currencies'));
    }

    public function create()
    {
        return view('currencies.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:currencies',
            'symbol' => 'required|string|max:255',
            'exchange_rate' => 'required|numeric|min:0',
            'is_default' => 'nullable|boolean',
        ]);

        if ($request->is_default) {
            Currency::where('is_default', true)->update(['is_default' => false]);
        }

        Currency::create($request->all());

        return redirect()->route('currencies.index')->with('success', __('main.currency_added_successfully'));
    }

    public function edit(Currency $currency)
    {
        return view('currencies.edit', compact('currency'));
    }

    public function update(Request $request, Currency $currency)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:currencies,code,' . $currency->id,
            'symbol' => 'required|string|max:255',
            'exchange_rate' => 'required|numeric|min:0',
            'is_default' => 'nullable|boolean',
        ]);

        if ($request->is_default) {
            Currency::where('is_default', true)->update(['is_default' => false]);
        }

        $currency->update($request->all());

        return redirect()->route('currencies.index')->with('success', __('main.currency_updated_successfully'));
    }

    public function destroy(Currency $currency)
    {
        $currency->delete();
        return redirect()->route('currencies.index')->with('success', __('main.currency_deleted_successfully'));
    }
}
