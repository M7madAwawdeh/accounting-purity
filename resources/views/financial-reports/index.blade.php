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
                     @if($reportData)
                     <p class="mb-0"><strong>{{ __('main.report_for_period') }}:</strong> {{ $reportData['start_date'] }} {{ __('main.to') }} {{ $reportData['end_date'] }}</p>
                    @endif
                </div>
            </div>
            <hr>
        </div>
        
        <div class="d-flex justify-content-between align-items-center mb-4 no-print">
            <h1 class="h3 mb-0 text-gray-800">{{ __('main.financial_reports') }}</h1>
        </div>

        <div class="card shadow-sm mb-4 no-print">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('main.select_period') }}</h6>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('financial-reports.index') }}">
                    <div class="row align-items-end">
                        <div class="col-md-3">
                            <label for="report_type">{{ __('main.report_type') }}</label>
                            <select name="report_type" id="report_type" class="form-control" required>
                                <option value="summary" {{ request('report_type', 'summary') == 'summary' ? 'selected' : '' }}>{{ __('main.summary_report') }}</option>
                                <option value="payment_vouchers" {{ request('report_type') == 'payment_vouchers' ? 'selected' : '' }}>{{ __('main.payment_vouchers_report') }}</option>
                                <option value="expense_vouchers" {{ request('report_type') == 'expense_vouchers' ? 'selected' : '' }}>{{ __('main.expense_vouchers_report') }}</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="start_date">{{ __('main.from_date') }}</label>
                            <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date', \Carbon\Carbon::now()->startOfMonth()->toDateString()) }}" required>
                        </div>
                        <div class="col-md-3">
                            <label for="end_date">{{ __('main.to_date') }}</label>
                            <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date', \Carbon\Carbon::now()->endOfMonth()->toDateString()) }}" required>
                        </div>
                        <div class="col-md-3">
                             <button type="submit" class="btn btn-primary w-100">{{ __('main.generate_report') }}</button>
                        </div>
                    </div>
                     <div id="additional-filters" class="row mt-3" style="display: {{ request('report_type', 'summary') != 'summary' ? 'flex' : 'none' }};">
                        <div class="col-md-3">
                            <label for="account_id">{{ __('main.account') }}</label>
                            <select name="account_id" id="account_id" class="form-control">
                                <option value="">{{ __('main.all_accounts') }}</option>
                                <optgroup label="{{ __('main.cash_boxes') }}">
                                    @foreach($cashBoxes as $box)
                                        <option value="CashBox-{{ $box->id }}" {{ request('account_id') == 'CashBox-'.$box->id ? 'selected' : '' }}>{{ $box->name }}</option>
                                    @endforeach
                                </optgroup>
                                <optgroup label="{{ __('main.banks') }}">
                                    @foreach($banks as $bank)
                                        <option value="Bank-{{ $bank->id }}" {{ request('account_id') == 'Bank-'.$bank->id ? 'selected' : '' }}>{{ $bank->name }}</option>
                                    @endforeach
                                </optgroup>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="project_id">{{ __('main.project') }}</label>
                            <select name="project_id" id="project_id" class="form-control">
                                <option value="">{{ __('main.all_projects') }}</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>{{ $project->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if($reportData)
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center no-print">
                    <h5 class="mb-0">
                        {{ __('main.report_for_period') }}: {{ $reportData['start_date'] }} {{ __('main.to') }} {{ $reportData['end_date'] }}
                    </h5>
                    <button onclick="window.print()" class="btn btn-sm btn-outline-secondary">{{ __('main.print_report') }}</button>
                </div>
                <div class="card-body">
                    @if($reportData['report_type'] === 'summary')
                        @foreach(collect($reportData['currency_totals'])->chunk(3) as $chunk)
                        <div class="row">
                             @foreach($chunk as $currency => $totals)
                             <div class="col-xl-4 col-md-6 mb-4">
                                <div class="card border-left-primary shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">{{ __('main.net_balance') }} ({{$currency}})</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totals['net_balance'], 2) }} {{ $totals['currency_symbol'] }}</div>
                                            </div>
                                            <div class="col-auto"><i class="fas fa-balance-scale fa-2x text-gray-300"></i></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endforeach

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="card shadow mb-4">
                                    <div class="card-header"><h6 class="m-0 font-weight-bold text-success">{{ __('main.income') }}</h6></div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm table-hover">
                                                <tbody>
                                                    @foreach($reportData['donations'] as $item)
                                                    <tr>
                                                        <td>{{ $item->date }}</td>
                                                        <td>{{ $item->donor_name }}</td>
                                                        <td>{{ __('main.donation') }}</td>
                                                        <td class="text-right">{{ number_format($item->amount, 2) }} {{ getCurrencyName($item->currency, true) }}</td>
                                                    </tr>
                                                    @endforeach
                                                    @foreach($reportData['payment_vouchers'] as $item)
                                                    <tr>
                                                        <td>{{ $item->date }}</td>
                                                        <td>{{ $item->payer }}</td>
                                                        <td>{{ __('main.payment_voucher') }}</td>
                                                        <td class="text-right">{{ number_format($item->amount, 2) }} {{ getCurrencyName($item->currency, true) }}</td>
                                                    </tr>
                                                    @endforeach
                                                    @if($reportData['donations']->isEmpty() && $reportData['payment_vouchers']->isEmpty())
                                                    <tr><td colspan="4" class="text-center">{{ __('main.no_income_records') }}</td></tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="card shadow mb-4">
                                     <div class="card-header"><h6 class="m-0 font-weight-bold text-danger">{{ __('main.expenses') }}</h6></div>
                                     <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm table-hover">
                                                <tbody>
                                                    @foreach($reportData['expense_vouchers'] as $item)
                                                    <tr>
                                                        <td>{{ $item->date }}</td>
                                                        <td>{{ $item->recipient }}</td>
                                                        <td>{{ __('main.expense_voucher') }}</td>
                                                        <td class="text-right">{{ number_format($item->amount, 2) }} {{ getCurrencyName($item->currency, true) }}</td>
                                                    </tr>
                                                    @endforeach
                                                    @if($reportData['expense_vouchers']->isEmpty())
                                                    <tr><td colspan="4" class="text-center">{{ __('main.no_expense_records') }}</td></tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        @php
                            $isPayment = $reportData['report_type'] === 'payment_vouchers';
                            $items = $reportData['items'];
                            $currencyGroups = $items->groupBy('currency');
                        @endphp
                        
                        @foreach($currencyGroups as $currency => $groupedItems)
                         <div class="card shadow mb-4">
                            <div class="card-header"><h6 class="m-0 font-weight-bold text-primary">{{ $isPayment ? __('main.payment_vouchers_report') : __('main.expense_vouchers_report') }} ({{ $currency }})</h6></div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-sm">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>{{ __('main.date') }}</th>
                                                <th>{{ $isPayment ? __('main.payer') : __('main.recipient') }}</th>
                                                <th>{{ __('main.project') }}</th>
                                                <th>{{ __('main.account') }}</th>
                                                <th class="text-right">{{ __('main.amount') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($groupedItems as $item)
                                                <tr>
                                                    <td>{{ $item->date }}</td>
                                                    <td>{{ $isPayment ? $item->payer : $item->recipient }}</td>
                                                    <td>{{ $item->project->name ?? '-' }}</td>
                                                    <td>{{ $item->accountable->name ?? '-' }}</td>
                                                    <td class="text-right">{{ number_format($item->amount, 2) }} {{ getCurrencyName($item->currency, true) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="4" class="text-right"><strong>{{ __('main.total_amount') }}</strong></td>
                                                <td class="text-right"><strong>{{ number_format($groupedItems->sum('amount'), 2) }} {{ getCurrencyName($currency, true) }}</strong></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @endif
                </div>
            </div>
        @else
            <div class="alert alert-info no-print">
                {{ __('main.please_select_period_to_generate_report') }}
            </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const reportType = document.getElementById('report_type');
        const additionalFilters = document.getElementById('additional-filters');

        reportType.addEventListener('change', function() {
            if (this.value === 'summary') {
                additionalFilters.style.display = 'none';
            } else {
                additionalFilters.style.display = 'flex';
            }
        });
    });
</script>
@endsection 