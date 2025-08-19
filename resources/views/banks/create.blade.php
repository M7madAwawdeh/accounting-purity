@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('main.add_bank') }}</h1>
        <a href="{{ route('banks.index') }}" class="btn btn-secondary btn-sm">{{ __('main.back_to_list') }}</a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('main.bank_details') }}</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('banks.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">{{ __('main.name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="account_number">{{ __('main.account_number') }} <span class="text-danger">*</span></label>
                            <input type="text" name="account_number" id="account_number" class="form-control @error('account_number') is-invalid @enderror" value="{{ old('account_number') }}" required>
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
                            <input type="text" name="iban" id="iban" class="form-control @error('iban') is-invalid @enderror" value="{{ old('iban') }}">
                            @error('iban')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="swift_code">{{ __('main.swift_code') }}</label>
                            <input type="text" name="swift_code" id="swift_code" class="form-control @error('swift_code') is-invalid @enderror" value="{{ old('swift_code') }}">
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
                            <input type="text" name="contact_person" id="contact_person" class="form-control @error('contact_person') is-invalid @enderror" value="{{ old('contact_person') }}">
                            @error('contact_person')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                     <div class="col-md-6">
                        <div class="form-group">
                            <label for="phone">{{ __('main.phone') }}</label>
                            <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group mt-3">
                    <label for="address">{{ __('main.address') }}</label>
                    <input type="text" name="address" id="address" class="form-control @error('address') is-invalid @enderror" value="{{ old('address') }}">
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <hr class="my-4">

                <h6 class="font-weight-bold">{{ __('main.currencies_and_balances') }} <span class="text-danger">*</span></h6>
                <div id="currency-list" class="mb-3">
                    <!-- Dynamic currency rows will be added here -->
                </div>
                <div class="row">
                    <div class="col-md-5">
                         <select id="currency-selector" class="form-control">
                            <option value="">{{ __('main.select_currency') }}</option>
                            @foreach ($currencies as $currency)
                                <option value="{{ $currency->id }}" data-name="{{ $currency->name }} ({{ $currency->code }})">{{ $currency->name }} ({{ $currency->code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                         <button type="button" id="add-currency-btn" class="btn btn-primary btn-block">{{ __('main.add') }}</button>
                    </div>
                </div>
                @error('currencies')
                    <div class="text-danger mt-2">{{ $message }}</div>
                @enderror


                <div class="mt-4">
                    <button type="submit" class="btn btn-success">{{ __('main.save') }}</button>
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
    let currencyIndex = 0;
    const addCurrencyBtn = document.getElementById('add-currency-btn');
    const currencySelector = document.getElementById('currency-selector');
    const currencyList = document.getElementById('currency-list');

    addCurrencyBtn.addEventListener('click', function () {
        const selectedOption = currencySelector.options[currencySelector.selectedIndex];
        if (!selectedOption.value) return;

        const currencyId = selectedOption.value;
        const currencyName = selectedOption.dataset.name;

        // Prevent adding the same currency twice
        if (document.querySelector(`input[name="currencies[${currencyIndex-1}][id]"][value="${currencyId}"]`)) {
             alert('{{ __('main.currency_already_added') }}');
             return;
        }

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
                    <button type="button" class="btn btn-danger btn-block remove-currency-btn" data-index="${currencyIndex}">{{ __('main.remove') }}</button>
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
            const row = document.getElementById(`currency-row-${index}`);
            const currencyId = row.querySelector('input[type="hidden"]').value;
            row.remove();
            
            // Re-enable the option in the selector
            const optionToEnable = currencySelector.querySelector(`option[value="${currencyId}"]`);
            if(optionToEnable) {
                optionToEnable.disabled = false;
            }
        }
    });
});
</script>
@endpush 