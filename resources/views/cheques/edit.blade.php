@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('main.edit_cheque') }}</h1>
        <a href="{{ route('cheques.index') }}" class="btn btn-secondary btn-sm">{{ __('main.back_to_list') }}</a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('main.cheque_number') }}: {{ $cheque->number }}</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('cheques.update', $cheque->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="status">{{ __('main.status') }} <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                                <option value="pending" {{ old('status', $cheque->status) == 'pending' ? 'selected' : '' }}>{{ __('main.cheque_status_pending') }}</option>
                                <option value="cleared" {{ old('status', $cheque->status) == 'cleared' ? 'selected' : '' }}>{{ __('main.cheque_status_cleared') }}</option>
                                <option value="bounced" {{ old('status', $cheque->status) == 'bounced' ? 'selected' : '' }}>{{ __('main.cheque_status_bounced') }}</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-12">
                         <p><strong>{{ __('main.bank') }}:</strong> {{ $cheque->bank->name }}</p>
                         <p><strong>{{ __('main.amount') }}:</strong> {{ number_format($cheque->amount, 2) }} {{ getCurrencyName($cheque->chequeable->currency, true) }}</p>
                         <p><strong>{{ __('main.due_date') }}:</strong> {{ $cheque->due_date }}</p>
                    </div>
                </div>


                <div class="mt-4">
                    <button type="submit" class="btn btn-success">{{ __('main.update') }}</button>
                    <a href="{{ route('cheques.index') }}" class="btn btn-secondary">{{ __('main.cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 