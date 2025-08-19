@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>{{ __('main.currencies') }}</h1>
        <a href="{{ route('currencies.create') }}" class="btn btn-primary">{{ __('main.add_currency') }}</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('main.name') }}</th>
                            <th>{{ __('main.code') }}</th>
                            <th>{{ __('main.symbol') }}</th>
                            <th>{{ __('main.exchange_rate') }}</th>
                            <th>{{ __('main.is_default') }}</th>
                            <th class="text-center">{{ __('main.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($currencies as $currency)
                            <tr>
                                <td>{{ $currency->name }}</td>
                                <td>{{ $currency->code }}</td>
                                <td>{{ $currency->symbol }}</td>
                                <td>{{ $currency->exchange_rate }}</td>
                                <td>
                                    @if($currency->is_default)
                                        <span class="badge bg-success">{{ __('main.yes') }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ __('main.no') }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('currencies.edit', $currency->id) }}" class="btn btn-sm btn-outline-primary">{{ __('main.edit') }}</a>
                                    <form action="{{ route('currencies.destroy', $currency->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('main.are_you_sure_delete') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('main.delete') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">{{ __('main.no_currencies_found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
