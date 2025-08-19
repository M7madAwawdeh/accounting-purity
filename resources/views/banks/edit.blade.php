@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('main.edit_bank') }}</h1>
        <a href="{{ route('banks.index') }}" class="btn btn-secondary btn-sm">{{ __('main.back_to_list') }}</a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('main.bank_details') }}</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('banks.update', $bank->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">{{ __('main.name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $bank->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="account_number">{{ __('main.account_number') }} <span class="text-danger">*</span></label>
                            <input type="text" name="account_number" id="account_number" class="form-control @error('account_number') is-invalid @enderror" value="{{ old('account_number', $bank->account_number) }}" required>
                            @error('account_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="iban">{{ __('main.iban') }}</label>
                            <input type="text" name="iban" id="iban" class="form-control @error('iban') is-invalid @enderror" value="{{ old('iban', $bank->iban) }}">
                            @error('iban')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="swift_code">{{ __('main.swift_code') }}</label>
                            <input type="text" name="swift_code" id="swift_code" class="form-control @error('swift_code') is-invalid @enderror" value="{{ old('swift_code', $bank->swift_code) }}">
                            @error('swift_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="contact_person">{{ __('main.contact_person') }}</label>
                            <input type="text" name="contact_person" id="contact_person" class="form-control @error('contact_person') is-invalid @enderror" value="{{ old('contact_person', $bank->contact_person) }}">
                            @error('contact_person')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                     <div class="col-md-6">
                        <div class="form-group">
                            <label for="phone">{{ __('main.phone') }}</label>
                            <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $bank->phone) }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group mt-3">
                    <label for="address">{{ __('main.address') }}</label>
                    <input type="text" name="address" id="address" class="form-control @error('address') is-invalid @enderror" value="{{ old('address', $bank->address) }}">
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <hr class="my-4">

                <h6 class="font-weight-bold">{{ __('main.currencies_and_balances') }}</h6>
                 <div id="currency-list" class="mb-3">
                    @foreach ($bank->currencies as $index => $currency)
                        <div class="row mb-2 align-items-center" id="currency-row-{{ $index }}">
                            <div class="col-md-5">
                                <input type="hidden" name="currencies[{{ $index }}][id]" value="{{ $currency->id }}">
                                <input type="text" class="form-control" value="{{ $currency->name }} ({{ $currency->code }})" readonly>
                            </div>
                            <div class="col-md-5">
                                <input type="number" name="currencies[{{ $index }}][balance]" class="form-control" placeholder="{{ __('main.balance') }}" value="{{ $currency->pivot->balance }}" step="0.01" required>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger btn-block remove-currency-btn" data-index="{{ $index }}" data-id="{{ $currency->id }}">{{ __('main.remove') }}</button>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="row">
                    <div class="col-md-5">
                         <select id="currency-selector" class="form-control">
                            <option value="">{{ __('main.select_currency') }}</option>
                            @foreach ($currencies as $currency)
                                 <option value="{{ $currency->id }}" data-name="{{ $currency->name }} ({{ $currency->code }})" {{ $bank->currencies->contains($currency->id) ? 'disabled' : '' }}>{{ $currency->name }} ({{ $currency->code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                         <button type="button" id="add-currency-btn" class="btn btn-primary btn-block">{{ __('main.add') }}</button>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-success">{{ __('main.update') }}</button>
                    <a href="{{ route('banks.index') }}" class="btn btn-secondary">{{ __('main.cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    let currencyIndex = {{ $bank->currencies->count() }};
    const addCurrencyBtn = document.getElementById('add-currency-btn');
    const currencySelector = document.getElementById('currency-selector');
    const currencyList = document.getElementById('currency-list');

    addCurrencyBtn.addEventListener('click', function () {
        const selectedOption = currencySelector.options[currencySelector.selectedIndex];
        if (!selectedOption.value || selectedOption.disabled) return;

        const currencyId = selectedOption.value;
        const currencyName = selectedOption.dataset.name;

        const newRow = `
            <div class="row mb-2 align-items-center" id="currency-row-${currencyIndex}">
                <div class="col-md-5">
                     <input type="hidden" name="currencies[${currencyIndex}][id]" value="${currencyId}">
                     <input type="text" class="form-control" value="${currencyName}" readonly>
                </div>
                <div class="col-md-5">
                    <input type="number" name="currencies[${currencyIndex}][balance]" class="form-control" placeholder="{{ __('main.initial_balance') }}" value="0.00" step="0.01" required>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger btn-block remove-currency-btn" data-index="${currencyIndex}" data-id="${currencyId}">{{ __('main.remove') }}</button>
                </div>
            </div>
        `;
        currencyList.insertAdjacentHTML('beforeend', newRow);
        currencyIndex++;
        selectedOption.disabled = true;
        currencySelector.selectedIndex = 0;
    });

    currencyList.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-currency-btn')) {
            const index = e.target.dataset.index;
            const currencyId = e.target.dataset.id;
            const row = document.getElementById(`currency-row-${index}`);
            if(row) row.remove();
            
            const optionToEnable = currencySelector.querySelector(`option[value="${currencyId}"]`);
            if(optionToEnable) {
                optionToEnable.disabled = false;
            }
        }
    });
});
</script>
@endpush 