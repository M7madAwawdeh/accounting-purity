@extends('layouts.app')

@section('content')
<style>
    @media print {
        body * { visibility: hidden; }
        #print-area, #print-area * { visibility: visible; }
        #print-area { position: absolute; left: 0; top: 0; width: 100%; }
        .no-print { display: none !important; }
        #print-area * { color: black !important; }
        #print-area .card, #print-area .alert { box-shadow: none !important; border: 1px solid #dee2e6 !important; }
        .table td, .table th { padding: .5rem; }
    }
</style>

<div class="container-fluid">
     <div id="print-area">
        {{-- Print Header --}}
        <div class="d-none d-print-block text-center mb-4">
             <div class="row">
                <div class="col-4 text-left">
                     @if(config('settings.logo'))
                        <img src="{{ asset('storage/' . config('settings.logo')) }}" alt="Logo" width="100">
                    @endif
                </div>
                 <div class="col-4 text-center">
                    <h4 class="mb-0">{{ config('settings.app_name', config('app.name', 'Laravel')) }}</h4>
                    <p class="mb-0">{{ config('settings.address') }}</p>
                    @if(is_array(config('settings.phone_numbers')))
                        @foreach(config('settings.phone_numbers') as $phone)
                            <p class="mb-0 d-inline-block mr-3">{{ $phone['number'] }}</p>
                        @endforeach
                    @endif
                </div>
                <div class="col-4 text-right">
                    <p class="mb-0"><strong>{{ __('main.date') }}:</strong> {{ now()->format('Y-m-d') }}</p>
                     @if(isset($account))
                     <p class="mb-0"><strong>{{ __('main.report_for_account') }}:</strong> {{ $account->name }}</p>
                    @endif
                </div>
            </div>
            <hr>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4 no-print">
            <h1 class="h3 mb-0 text-gray-800">{{ __('main.account_statement') }}</h1>
        </div>

        <div class="card shadow-sm mb-4 no-print">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('main.select_period') }}</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('account-report.generate') }}">
                    @csrf
                    <div class="row align-items-end">
                        <div class="col-md-3">
                            <label for="account">{{ __('main.account') }}</label>
                            <select name="account" id="account" class="form-control" required>
                                <option value="">{{ __('main.select_account') }}</option>
                                <optgroup label="{{ __('main.cash_boxes') }}">
                                    @foreach($cashBoxes as $cashBox)
                                        <option value="cashBox-{{ $cashBox->id }}" {{ (isset($inputs['account']) && $inputs['account'] == 'cashBox-'.$cashBox->id) ? 'selected' : '' }}>{{ $cashBox->name }}</option>
                                    @endforeach
                                </optgroup>
                                <optgroup label="{{ __('main.banks') }}">
                                    @foreach($banks as $bank)
                                        <option value="bank-{{ $bank->id }}" {{ (isset($inputs['account']) && $inputs['account'] == 'bank-'.$bank->id) ? 'selected' : '' }}>{{ $bank->name }}</option>
                                    @endforeach
                                </optgroup>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="currency_id">{{ __('main.currency') }}</label>
                            <select name="currency_id" id="currency_id" class="form-control" required>
                                <option value="">{{ __('main.select_currency') }}</option>
                                <option value="all" {{ (isset($inputs['currency_id']) && $inputs['currency_id'] == 'all') ? 'selected' : '' }}>{{ __('main.all_currencies') }}</option>
                                @foreach($currencies as $currency)
                                    <option value="{{ $currency->id }}" {{ (isset($inputs['currency_id']) && $inputs['currency_id'] == $currency->id) ? 'selected' : '' }}>{{ $currency->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="from_date">{{ __('main.from_date') }}</label>
                            <input type="date" name="from_date" id="from_date" class="form-control" value="{{ $inputs['from_date'] ?? '' }}">
                        </div>
                        <div class="col-md-3">
                            <label for="to_date">{{ __('main.to_date') }}</label>
                            <input type="date" name="to_date" id="to_date" class="form-control" value="{{ $inputs['to_date'] ?? '' }}">
                        </div>
                        <div class="col-md-1">
                            <button type="submit" class="btn btn-primary w-100">{{ __('main.generate_report') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if(isset($transactions))
        <div class="card shadow-sm mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('main.report_for_account') }} "{{ $account->name }}" ({{$selected_currency->name ?? __('main.all_currencies')}})</h6>
                <button class="btn btn-secondary btn-sm no-print" onclick="window.print()">{{ __('main.print_report') }}</button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="thead-dark">
                            <tr>
                                <th>#</th>
                                <th>{{ __('main.date') }}</th>
                                <th>{{ __('main.details') }}</th>
                                <th>{{ __('main.type') }}</th>
                                <th class="text-right">{{ __('main.debit') }}</th>
                                <th class="text-right">{{ __('main.credit') }}</th>
                                <th class="text-right">{{ __('main.balance') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php 
                                $balances = []; 
                                $isAllCurrencies = !$selected_currency;
                            @endphp
                            @forelse ($transactions as $index => $transaction)
                            @php 
                                $currencyCode = $isAllCurrencies ? $transaction->currency : $selected_currency->code;
                                if (!isset($balances[$currencyCode])) {
                                    $balances[$currencyCode] = 0;
                                }
                                $balances[$currencyCode] += $transaction->amount;
                                $currencySymbol = getCurrencyName($currencyCode, true);
                            @endphp
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $transaction->date }}</td>
                                    <td>{{ $transaction->details }}</td>
                                    <td>{{ __('main.' . $transaction->type) }}</td>
                                    <td class="text-right text-danger">{{ $transaction->amount < 0 ? number_format(abs($transaction->amount), 2) . ' ' . $currencySymbol : '-' }}</td>
                                    <td class="text-right text-success">{{ $transaction->amount > 0 ? number_format($transaction->amount, 2) . ' ' . $currencySymbol : '-' }}</td>
                                    <td class="text-right font-weight-bold">{{ number_format($balances[$currencyCode], 2) . ' ' . $currencySymbol }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">{{ __('main.no_transactions_found_in_period') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="font-weight-bold">
                            @if($isAllCurrencies)
                                @foreach($balances as $currencyCode => $balance)
                                    <tr>
                                        <td colspan="6" class="text-right">{{ __('main.final_balance') }} ({{$currencyCode}})</td>
                                        <td class="text-right">{{ number_format($balance, 2) }} {{ getCurrencyName($currencyCode, true) }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6" class="text-right">{{ __('main.final_balance') }}</td>
                                    <td class="text-right">{{ number_format($transactions->sum('amount'), 2) . ' ' . ($selected_currency ? $selected_currency->symbol : '') }}</td>
                                </tr>
                            @endif
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        @else
         <div class="alert alert-info no-print">
            {{ __('main.please_select_period_to_generate_report') }}
        </div>
        @endif
    </div>
</div>
@endsection 