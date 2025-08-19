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
    }
</style>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('main.payment_voucher_details') }}</h1>
        <div>
            <button onclick="window.print()" class="btn btn-sm btn-outline-secondary no-print">
                <i class="fas fa-print"></i> {{ __('main.print') }}
            </button>
            <a href="{{ route('payment-vouchers.index') }}" class="btn btn-sm btn-secondary no-print">{{ __('main.back_to_list') }}</a>
        </div>
    </div>

    <div id="print-area">
        {{-- Print Header --}}
        <div class="d-none d-print-block text-center mb-4">
            @if(config('settings.logo'))
                <img src="{{ asset('storage/' . config('settings.logo')) }}" alt="Logo" width="150" class="mb-3">
            @endif
            <h3>{{ config('settings.app_name', config('app.name', 'Laravel')) }}</h3>
            <p class="mb-0">{{ config('settings.address') }}</p>
            @if(is_array(config('settings.phone_numbers')))
                @foreach(config('settings.phone_numbers') as $phone)
                    <p class="mb-0 d-inline-block mr-3">{{ $phone['name'] }}: {{ $phone['number'] }}</p>
                @endforeach
            @endif
             @if(config('settings.tax_number'))
                <p>{{ __('main.tax_number') }}: {{ config('settings.tax_number') }}</p>
            @endif
            <hr>
            <h4 class="mt-2">{{ __('main.payment_voucher') }}</h4>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('main.voucher_details') }} #{{ $paymentVoucher->id }}</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>{{ __('main.date') }}:</strong> {{ $paymentVoucher->date }}</p>
                        <p><strong>{{ __('main.payer') }}:</strong> {{ $paymentVoucher->payer }}</p>
                        <p><strong>{{ __('main.amount') }}:</strong> <span class="badge badge-success p-2">{{ number_format($paymentVoucher->amount, 2) }} {{ getCurrencyName($paymentVoucher->currency) }}</span></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>{{ __('main.payment_method') }}:</strong> {{ __('main.payment_method_' . $paymentVoucher->payment_method) }}</p>
                        <p><strong>{{ __('main.account') }}:</strong> 
                            @if($paymentVoucher->accountable)
                                <a href="{{ $paymentVoucher->accountable->getViewLink() }}" target="_blank">{{ $paymentVoucher->accountable->name }}</a>
                            @else
                                N/A
                            @endif
                        </p>
                        <p><strong>{{ __('main.project') }}:</strong> 
                            @if($paymentVoucher->project)
                                <a href="{{ route('projects.show', $paymentVoucher->project_id) }}" target="_blank">{{ $paymentVoucher->project->name }}</a>
                            @else
                                -
                            @endif
                        </p>
                    </div>
                </div>

                @if($paymentVoucher->payment_method == 'cheque' && $paymentVoucher->cheque)
                <hr>
                <h5>{{ __('main.cheque_details') }}</h5>
                <div class="row">
                    <div class="col-md-4">
                        <p><strong>{{ __('main.cheque_number') }}:</strong> {{ $paymentVoucher->cheque->number }}</p>
                    </div>
                    <div class="col-md-4">
                        <p><strong>{{ __('main.cheque_account_number') }}:</strong> {{ $paymentVoucher->cheque->account_number }}</p>
                    </div>
                    <div class="col-md-4">
                        <p><strong>{{ __('main.due_date') }}:</strong> {{ $paymentVoucher->cheque->due_date }}</p>
                    </div>
                </div>
                @endif
                
                <hr>
                <p><strong>{{ __('main.description') }}:</strong></p>
                <p>{{ $paymentVoucher->description ?? 'N/A' }}</p>

                <div class="d-none d-print-block mt-5">
                    <div class="row">
                        <div class="col-6 text-center">
                            <p>_________________________</p>
                            <p>{{ __('main.recipient_signature') }}</p>
                        </div>
                        <div class="col-6 text-center">
                            <p>_________________________</p>
                            <p>{{ __('main.accountant_signature') }}</p>
                        </div>
                    </div>
                </div>

                <hr>
                <div class="row small text-muted">
                    <div class="col-md-6">
                        <p><strong>{{ __('main.created_at') }}:</strong> {{ $paymentVoucher->created_at->format('Y-m-d H:i A') }}</p>
                    </div>
                    <div class="col-md-6 text-md-right">
                        <p><strong>{{ __('main.last_updated_at') }}:</strong> {{ $paymentVoucher->updated_at->format('Y-m-d H:i A') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($print)
<script>
    window.onload = function() {
        window.print();
    }
</script>
@endif

@endsection 