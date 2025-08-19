@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('main.cash_boxes') }}</h1>
        <a href="{{ route('cash-boxes.create') }}" class="btn btn-primary btn-sm shadow-sm">{{ __('main.add_cash_box') }}</a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('main.search') }}</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('cash-boxes.index') }}" method="GET">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="{{ __('main.search_by_name') }}" value="{{ request('search') }}">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-outline-secondary">{{ __('main.search') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
         <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('main.cash_boxes_list') }}</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>{{ __('main.name') }}</th>
                            <th>{{ __('main.currencies_and_balances') }}</th>
                            <th class="text-center">{{ __('main.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($cashBoxes as $cashBox)
                            <tr>
                                <td><a href="{{ route('cash-boxes.show', $cashBox->id) }}">{{ $cashBox->name }}</a></td>
                                <td>
                                    @foreach($cashBox->currencies as $currency)
                                        <span class="badge badge-pill badge-success mr-1">
                                            {{ $currency->name }}: {{ number_format($currency->pivot->balance, 2) }} {{ $currency->symbol }}
                                        </span>
                                    @endforeach
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('cash-boxes.show', $cashBox->id) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                    <a href="{{ route('cash-boxes.edit', $cashBox->id) }}" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('cash-boxes.destroy', $cashBox->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('{{ __('main.are_you_sure') }}')"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">{{ __('main.no_cash_boxes_found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-3">
                {{ $cashBoxes->links() }}
            </div>
        </div>
    </div>
</div>
@endsection 