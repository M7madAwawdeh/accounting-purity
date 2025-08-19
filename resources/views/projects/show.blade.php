@extends('layouts.app')

@section('content')
<style>
    @media print {
        body * { visibility: hidden; }
        #print-area, #print-area * { visibility: visible; }
        #print-area { position: absolute; left: 0; top: 0; width: 100%; }
        .no-print { display: none !important; }
        #print-area * { color: black !important; }
        #print-area .card { box-shadow: none !important; border: 1px solid #dee2e6 !important; }
        .table td, .table th { padding: .5rem; }
    }
</style>
<div class="container-fluid" id="project-report">
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
                    <p class="mb-0"><strong>{{ __('main.project') }}:</strong> {{ $project->name }}</p>
                </div>
            </div>
            <hr>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4 no-print">
            <h1>{{ __('main.project_report') }}: {{ $project->name }}</h1>
            <div>
                <button onclick="printReport()" class="btn btn-secondary">{{ __('main.print') }}</button>
                <a href="{{ route('projects.index') }}" class="btn btn-secondary">{{ __('main.back_to_list') }}</a>
            </div>
        </div>

        <div class="card shadow-sm mb-4 no-print">
            <div class="card-body">
                <form id="filter-form" method="GET" action="{{ route('projects.show', $project) }}">
                    <div class="row align-items-end">
                        <div class="col-md-4">
                            <label for="start_date">{{ __('main.from_date') }}</label>
                            <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $startDate }}">
                        </div>
                        <div class="col-md-4">
                            <label for="end_date">{{ __('main.to_date') }}</label>
                            <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $endDate }}">
                        </div>
                         <div class="col-md-2">
                            <label for="currency">{{ __('main.currency') }}</label>
                            <select name="currency" id="currency" class="form-control">
                                <option value="">{{ __('main.all') }}</option>
                                @foreach($availableCurrencies as $currencyCode)
                                    <option value="{{ $currencyCode }}" {{ $currencyFilter == $currencyCode ? 'selected' : '' }}>{{ $currencyCode }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">{{ __('main.filter') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if(empty($currencyFilter))
            @foreach($currencyTotals as $currency => $totals)
            <div class="row">
                <div class="col-md-12">
                    <h4 class="mb-3">{{ __('main.financial_summary_for') }} {{ $currency }}</h4>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-success mb-3">
                        <div class="card-header">{{ __('main.total_income') }}</div>
                        <div class="card-body">
                            <h4 class="card-title">{{ number_format($totals['total_income'], 2) }} {{ $totals['currency_symbol'] }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-danger mb-3">
                        <div class="card-header">{{ __('main.total_expenses') }}</div>
                        <div class="card-body">
                            <h4 class="card-title">{{ number_format($totals['total_expenses'], 2) }} {{ $totals['currency_symbol'] }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-info mb-3">
                        <div class="card-header">{{ __('main.net_balance') }}</div>
                        <div class="card-body">
                            <h4 class="card-title">{{ number_format($totals['net_balance'], 2) }} {{ $totals['currency_symbol'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        @elseif(!empty($currencyTotals[$currencyFilter]))
             @php $totals = $currencyTotals[$currencyFilter]; @endphp
             <div class="row">
                <div class="col-md-12">
                    <h4 class="mb-3">{{ __('main.financial_summary_for') }} {{ $currencyFilter }}</h4>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-success mb-3">
                        <div class="card-header">{{ __('main.total_income') }}</div>
                        <div class="card-body">
                            <h4 class="card-title">{{ number_format($totals['total_income'], 2) }} {{ $totals['currency_symbol'] }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-danger mb-3">
                        <div class="card-header">{{ __('main.total_expenses') }}</div>
                        <div class="card-body">
                            <h4 class="card-title">{{ number_format($totals['total_expenses'], 2) }} {{ $totals['currency_symbol'] }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-info mb-3">
                        <div class="card-header">{{ __('main.net_balance') }}</div>
                        <div class="card-body">
                            <h4 class="card-title">{{ number_format($totals['net_balance'], 2) }} {{ $totals['currency_symbol'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        @endif


        <div class="card shadow-sm mt-4">
            <div class="card-header">
                <h4>{{ __('main.transactions_details') }}</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>{{ __('main.date') }}</th>
                                <th>{{ __('main.type') }}</th>
                                <th>{{ __('main.details') }}</th>
                                <th class="text-right">{{ __('main.income') }}</th>
                                <th class="text-right">{{ __('main.expense') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($allTransactions as $index => $transaction)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $transaction->date }}</td>
                                    <td><a href="{{ $transaction->transaction_link }}">{{ $transaction->transaction_type_display }}</a></td>
                                    <td>{{ $transaction->transaction_party }}</td>
                                    <td class="text-right text-success">{{ $transaction->debit == 0 ? number_format($transaction->credit, 2) . ' ' . getCurrencyName($transaction->currency, true) : '-' }}</td>
                                    <td class="text-right text-danger">{{ $transaction->credit == 0 ? number_format($transaction->debit, 2) . ' ' . getCurrencyName($transaction->currency, true) : '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">{{ __('main.no_transactions_found_period') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function printReport() {
        const form = document.getElementById('filter-form');
        const params = new URLSearchParams(new FormData(form));
        params.set('print', 'true');
        
        window.open(form.action + '?' + params.toString(), '_blank');
    }
</script>

@if($print)
<script>
    window.onload = function() {
        setTimeout(function () {
            window.print();
            window.onafterprint = function() {
                window.close();
            }
        }, 500);
    }
</script>
@endif
@endsection 