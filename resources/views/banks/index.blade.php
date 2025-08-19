@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('main.banks') }}</h1>
        <a href="{{ route('banks.create') }}" class="btn btn-primary btn-sm shadow-sm">{{ __('main.add_bank') }}</a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('main.search') }}</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('banks.index') }}" method="GET">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="{{ __('main.search_by_name_or_account') }}" value="{{ request('search') }}">
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
            <h6 class="m-0 font-weight-bold text-primary">{{ __('main.banks_list') }}</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>{{ __('main.name') }}</th>
                            <th>{{ __('main.account_number') }}</th>
                            <th>{{ __('main.currencies_and_balances') }}</th>
                            <th class="text-center">{{ __('main.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($banks as $bank)
                            <tr>
                                <td><a href="{{ route('banks.show', $bank->id) }}">{{ $bank->name }}</a></td>
                                <td>{{ $bank->account_number }}</td>
                                <td>
                                    @foreach($bank->currencies as $currency)
                                        <span class="badge badge-pill badge-info mr-1">
                                            {{ $currency->name }}: {{ number_format($currency->pivot->balance, 2) }} {{ $currency->symbol }}
                                        </span>
                                    @endforeach
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('banks.show', $bank->id) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                    <a href="{{ route('banks.edit', $bank->id) }}" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('banks.destroy', $bank->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('{{ __('main.are_you_sure') }}')"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">{{ __('main.no_banks_found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-3">
                {{ $banks->links() }}
            </div>
        </div>
    </div>
</div>
@endsection 