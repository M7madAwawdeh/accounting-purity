@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1>{{ __('main.edit_donation') }}</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('donations.update', $donation->id) }}" method="POST" id="transactionForm">
                @csrf
                @method('PUT')
                <input type="hidden" name="accountable_type" id="accountable_type" value="{{ old('accountable_type', $donation->accountable_type) }}">
                <input type="hidden" name="accountable_id" id="accountable_id" value="{{ old('accountable_id', $donation->accountable_id) }}">

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="donor_name">{{ __('main.donor_name') }}</label>
                            <input type="text" name="donor_name" class="form-control @error('donor_name') is-invalid @enderror" value="{{ old('donor_name', $donation->donor_name) }}" required>
                            @error('donor_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="date">{{ __('main.date') }}</label>
                            <input type="date" name="date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date', $donation->date) }}" required>
                            @error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="amount">{{ __('main.amount') }}</label>
                            <input type="number" name="amount" step="0.01" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount', $donation->amount) }}" required>
                            @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="currency">{{ __('main.currency') }}</label>
                            <select name="currency" id="currency" class="form-control @error('currency') is-invalid @enderror" required>
                                <option value="">{{ __('main.select_currency') }}</option>
                                @foreach($currencies as $currency)
                                    <option value="{{ $currency->code }}" {{ old('currency', $donation->currency) == $currency->code ? 'selected' : '' }}>{{ $currency->name }} ({{ $currency->symbol }})</option>
                                @endforeach
                            </select>
                            @error('currency')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="project_id">{{ __('main.project') }} ({{ __('main.optional') }})</label>
                            <select name="project_id" id="project_id" class="form-control @error('project_id') is-invalid @enderror">
                                <option value="">{{ __('main.select_project') }}</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" {{ old('project_id', $donation->project_id) == $project->id ? 'selected' : '' }}>{{ $project->name }}</option>
                                @endforeach
                            </select>
                            @error('project_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <div class="row" id="association_fee_row" style="display: none;">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="association_fee">{{ __('main.association_fee') }}</label>
                            <input type="number" name="association_fee" step="0.01" class="form-control @error('association_fee') is-invalid @enderror" value="{{ old('association_fee', $donation->association_fee) }}">
                            @error('association_fee')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="payment_method">{{ __('main.payment_method') }}</label>
                            <select name="payment_method" id="payment_method" class="form-control @error('payment_method') is-invalid @enderror" required>
                                <option value="">{{ __('main.select_payment_method') }}</option>
                                <option value="cash" {{ old('payment_method', $donation->payment_method) == 'cash' ? 'selected' : '' }}>{{ __('main.cash') }}</option>
                                <option value="bank_transfer" {{ old('payment_method', $donation->payment_method) == 'bank_transfer' ? 'selected' : '' }}>{{ __('main.bank_transfer') }}</option>
                                <option value="cheque" {{ old('payment_method', $donation->payment_method) == 'cheque' ? 'selected' : '' }}>{{ __('main.cheque') }}</option>
                            </select>
                            @error('payment_method')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group" id="account_selector" style="display: none;">
                            <label for="account">{{ __('main.account') }}</label>
                            <select id="account" class="form-control @error('accountable_id') is-invalid @enderror" required>
                                <!-- Options will be populated by JS -->
                            </select>
                            <small id="account_number_display" class="form-text text-muted" style="display: none;"></small>
                            @error('accountable_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <div id="cheque_details" style="display: none;">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="cheque_number">{{ __('main.cheque_number') }}</label>
                                <input type="text" name="number" class="form-control @error('number') is-invalid @enderror" value="{{ old('number', $donation->cheque->number ?? '') }}">
                                @error('number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="cheque_account_number">{{ __('main.cheque_account_number') }}</label>
                                <input type="text" name="cheque_account_number" id="cheque_account_number" class="form-control @error('cheque_account_number') is-invalid @enderror" value="{{ old('cheque_account_number', $donation->cheque->account_number ?? '') }}">
                                @error('cheque_account_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="cheque_due_date">{{ __('main.due_date') }}</label>
                                <input type="date" name="cheque_due_date" class="form-control @error('cheque_due_date') is-invalid @enderror" value="{{ old('cheque_due_date', $donation->cheque->due_date ?? '') }}">
                                @error('cheque_due_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">{{ __('main.description') }}</label>
                    <textarea name="description" class="form-control @error('description') is-invalid @enderror">{{ old('description', $donation->description) }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <button type="submit" class="btn btn-primary">{{ __('main.update') }}</button>
                <a href="{{ route('donations.index') }}" class="btn btn-secondary">{{ __('main.cancel') }}</a>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const paymentMethodSelect = document.getElementById('payment_method');
    const accountSelectorDiv = document.getElementById('account_selector');
    const accountSelect = document.getElementById('account');
    const accountNumberDisplay = document.getElementById('account_number_display');
    const accountableTypeInput = document.getElementById('accountable_type');
    const accountableIdInput = document.getElementById('accountable_id');
    const currencySelect = document.getElementById('currency');
    const chequeDetailsDiv = document.getElementById('cheque_details');
    const projectIdSelect = document.getElementById('project_id');
    const associationFeeRow = document.getElementById('association_fee_row');
    const defaultProjectId = '{{ $defaultProject ? $defaultProject->id : '' }}';

    const accounts = @json($accounts);

    const selectedAccountable = {
        type: '{{ old('accountable_type', $donation->accountable_type) }}',
        id: '{{ old('accountable_id', $donation->accountable_id) }}'
    };

    function toggleAssociationFee() {
        if (projectIdSelect.value && projectIdSelect.value != defaultProjectId) {
            associationFeeRow.style.display = 'flex';
        } else {
            associationFeeRow.style.display = 'none';
        }
    }

    function updateAccountOptions() {
        const method = paymentMethodSelect.value;
        let options = [];
        
        accountSelect.innerHTML = '<option value="">{{ __("main.select_account") }}</option>';
        accountNumberDisplay.style.display = 'none';
        
        if (method === 'cash') {
            options = accounts.cash;
            accountSelectorDiv.style.display = 'block';
            chequeDetailsDiv.style.display = 'none';
        } else if (method === 'bank_transfer' || method === 'cheque') {
            options = accounts.bank;
            accountSelectorDiv.style.display = 'block';
            chequeDetailsDiv.style.display = method === 'cheque' ? 'block' : 'none';
        } else {
            accountSelectorDiv.style.display = 'none';
            chequeDetailsDiv.style.display = 'none';
        }

        let selectedOptionRef = null;

        options.forEach(function(account) {
            const option = document.createElement('option');
            const value = `${account.type}-${account.id}`;
            option.value = value;
            option.textContent = account.name;
            if (account.account_number) {
                option.dataset.accountNumber = account.account_number;
            }
            if (selectedAccountable.type.replace(/\\\\/g, '\\') === account.type.replace(/\\\\/g, '\\') && selectedAccountable.id == account.id) {
                option.selected = true;
                selectedOptionRef = option;
            }
            accountSelect.appendChild(option);
        });

        if (selectedOptionRef && selectedOptionRef.dataset.accountNumber) {
            accountNumberDisplay.textContent = `{{ __('main.account_number') }}: ${selectedOptionRef.dataset.accountNumber}`;
            accountNumberDisplay.style.display = 'block';
        }
    }

    accountSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const [type, id] = this.value.split('-');
        accountableTypeInput.value = type;
        accountableIdInput.value = id;

        if (selectedOption.dataset.accountNumber) {
            accountNumberDisplay.textContent = `{{ __('main.account_number') }}: ${selectedOption.dataset.accountNumber}`;
            accountNumberDisplay.style.display = 'block';
        } else {
            accountNumberDisplay.style.display = 'none';
        }
    });

    paymentMethodSelect.addEventListener('change', updateAccountOptions);
    projectIdSelect.addEventListener('change', toggleAssociationFee);
    
    if(paymentMethodSelect.value) {
        updateAccountOptions();
    }
    toggleAssociationFee();
});
</script>
@endpush 