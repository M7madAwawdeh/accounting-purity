@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('main.cheques') }}</h1>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('main.filter') }}</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('cheques.index') }}" method="GET">
                <div class="row">
                    <div class="col-md-5">
                        <input type="text" name="search" class="form-control" placeholder="{{ __('main.search_by_cheque_number') }}" value="{{ request('search') }}">
                    </div>
                    <div class="col-md-5">
                        <select name="status" class="form-control">
                            <option value="">{{ __('main.all_statuses') }}</option>
                            @foreach(\App\Models\Cheque::STATUSES as $status)
                                <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>{{ __('main.cheque_status_' . $status) }}</option>
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

    <div class="card shadow-sm">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('main.cheques_list') }}</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive-md">
                <table class="table table-bordered table-striped table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>{{ __('main.cheque_number') }}</th>
                            <th>{{ __('main.bank') }}</th>
                            <th>{{ __('main.amount') }}</th>
                            <th>{{ __('main.due_date') }}</th>
                            <th>{{ __('main.type') }}</th>
                            <th>{{ __('main.status') }}</th>
                            <th>{{ __('main.linked_voucher') }}</th>
                            <th class="text-center">{{ __('main.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($cheques as $cheque)
                            <tr>
                                <td><a href="{{ route('cheques.show', $cheque) }}">{{ $cheque->number }}</a></td>
                                <td>{{ $cheque->bank->name }}</td>
                                <td>{{ number_format($cheque->amount, 2) }} {{ getCurrencyName($cheque->currency, true) }}</td>
                                <td>{{ $cheque->due_date }}</td>
                                <td>{{ __('main.cheque_type_' . $cheque->type) }}</td>
                                <td>
                                    <span class="badge badge-{{ \App\Models\Cheque::STATUS_COLOR[$cheque->status] ?? 'secondary' }}">
                                        {{ __('main.cheque_status_' . $cheque->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if ($cheque->chequeable)
                                        <a href="{{ route(getVoucherRouteName($cheque->chequeable_type) . '.show', $cheque->chequeable_id) }}" target="_blank">
                                            {{ __( 'main.' . strtolower(class_basename($cheque->chequeable_type))) }} #{{ $cheque->chequeable_id }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('cheques.show', $cheque) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                    <a href="{{ route('cheques.edit', $cheque) }}" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('cheques.destroy', $cheque) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger delete-btn"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">{{ __('main.no_cheques_found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-3">
                {{ $cheques->links() }}
            </div>
        </div>
    </div>
</div>
@endsection 