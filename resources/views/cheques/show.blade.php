@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('main.cheque_details') }}</h1>
        <a href="{{ route('cheques.index') }}" class="btn btn-secondary btn-sm">{{ __('main.back_to_list') }}</a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('main.cheque_number') }}: {{ $cheque->number }}</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>{{ __('main.bank') }}:</strong> {{ $cheque->bank->name }}</p>
                    <p><strong>{{ __('main.cheque_account_number') }}:</strong> {{ $cheque->account_number }}</p>
                    <p><strong>{{ __('main.amount') }}:</strong> {{ number_format($cheque->amount, 2) }} {{ getCurrencyName($cheque->chequeable->currency, true) }}</p>
                    <p><strong>{{ __('main.due_date') }}:</strong> {{ $cheque->due_date }}</p>
                    <p><strong>{{ __('main.status') }}:</strong> <span class="badge badge-{{ $cheque->status == 'cleared' ? 'success' : ($cheque->status == 'bounced' ? 'danger' : 'warning') }}">{{ __('main.cheque_status_' . $cheque->status) }}</span></p>
                </div>
                <div class="col-md-6">
                    @if($cheque->chequeable)
                         <p><strong>{{ __('main.recipient') }}:</strong> {{ $cheque->chequeable->recipient ?? $cheque->chequeable->payer ?? $cheque->chequeable->donor_name }}</p>
                         <p><strong>{{ __('main.description') }}:</strong> {{ $cheque->chequeable->description }}</p>
                         <p><strong>{{ __('main.linked_voucher') }}:</strong> 
                            <a href="{{ route(getVoucherRouteName($cheque->chequeable_type) . '.show', $cheque->chequeable->id) }}">
                                {{ __('main.' . strtolower(class_basename($cheque->chequeable_type))) }} #{{$cheque->chequeable->id}}
                            </a>
                        </p>
                    @endif
                </div>
                
            </div>
        </div>
    </div>
</div>
@endsection 