@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('main.cash_box_details') }}</h1>
        <div>
            <a href="{{ route('cash-boxes.index') }}" class="btn btn-secondary btn-sm">{{ __('main.back_to_list') }}</a>
            <a href="{{ route('cash-boxes.edit', $cashBox->id) }}" class="btn btn-primary btn-sm">{{ __('main.edit') }}</a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ $cashBox->name }}</h6>
                </div>
                <div class="card-body">
                    @if($cashBox->location)
                        <p><strong>{{ __('main.location') }}:</strong> {{ $cashBox->location }}</p>
                    @endif
                    @if($cashBox->manager)
                        <p><strong>{{ __('main.manager') }}:</strong> {{ $cashBox->manager }}</p>
                    @endif
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('main.currency_balances') }}</h6>
                </div>
                <div class="card-body">
                    @forelse($cashBox->currencies as $currency)
                        <h5>{{ $currency->name }}: {{ number_format($currency->pivot->balance, 2) }} {{ $currency->symbol }}</h5>
                    @empty
                        <p>{{ __('main.no_currencies_assigned') }}</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('main.transactions_history') }}</h6>
                </div>
                <div class="card-body">
                     <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                            <thead class="thead-dark">
                                <tr>
                                    <th>{{ __('main.date') }}</th>
                                    <th>{{ __('main.type') }}</th>
                                    <th>{{ __('main.details') }}</th>
                                    <th>{{ __('main.credit') }}</th>
                                    <th>{{ __('main.debit') }}</th>
                                    <th>{{ __('main.currency') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($transactions as $transaction)
                                    <tr>
                                        <td>{{ $transaction->date }}</td>
                                        <td>{{ $transaction->transaction_type }}</td>
                                        <td>{{ $transaction->transaction_details }}</td>
                                        <td class="text-success">{{ $transaction->credit > 0 ? number_format($transaction->credit, 2) : '-' }}</td>
                                        <td class="text-danger">{{ $transaction->debit > 0 ? number_format($transaction->debit, 2) : '-' }}</td>
                                        <td>{{ getCurrencyName($transaction->currency, true) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">{{ __('main.no_transactions_found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 