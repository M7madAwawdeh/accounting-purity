@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">{{ __('main.transfer_funds') }}</h1>
            <p class="text-muted mb-0">{{ __('main.transfer_funds_description') }}</p>
        </div>
        <div class="d-flex align-items-center">
            <i class="fas fa-exchange-alt text-primary me-2 fs-4"></i>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-xl-8 col-lg-10">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-gradient-primary text-white py-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-white bg-opacity-25 rounded-circle p-2 me-3">
                                <i class="fas fa-exchange-alt text-white fs-5"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="mb-0 fw-bold">{{ __('main.new_transfer') }}</h5>
                            <small class="opacity-75">{{ __('main.create_new_transfer') }}</small>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4 p-lg-5">
                    <form method="POST" action="{{ route('account-transfers.store') }}" class="needs-validation" novalidate>
                        @csrf
                        
                        <!-- Alert Messages -->
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-check-circle text-success me-2 fs-5"></i>
                                    <div>{{ session('success') }}</div>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-exclamation-triangle text-danger me-2 fs-5"></i>
                                    <strong>{{ __('main.validation_errors') }}</strong>
                                </div>
                                <ul class="mb-0 ps-4">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <div x-data="transferForm()" x-init="initData({{ json_encode($accounts) }})">
                            <!-- Account Selection Section -->
                            <div class="row g-4 mb-4">
                                <div class="col-12">
                                    <h6 class="text-primary fw-bold mb-3">
                                        <i class="fas fa-university me-2"></i>{{ __('main.account_selection') }}
                                    </h6>
                                </div>
                                
                                <!-- From Account -->
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label fw-semibold text-dark mb-2">
                                            <i class="fas fa-arrow-up text-danger me-1"></i>{{ __('main.from_account') }}
                                        </label>
                                        <select name="from_account_id" x-model="fromAccountId" class="form-select form-select-lg" required>
                                            <option value="">{{ __('main.select_account') }}</option>
                                            <optgroup label="{{ __('main.banks') }}" class="fw-semibold">
                                                @foreach($banksFormatted as $bank)
                                                    <option value="{{ $bank['id'] }}">{{ $bank['name'] }}</option>
                                                @endforeach
                                            </optgroup>
                                            <optgroup label="{{ __('main.cash_boxes') }}" class="fw-semibold">
                                                @foreach($cashBoxesFormatted as $cashBox)
                                                    <option value="{{ $cashBox['id'] }}">{{ $cashBox['name'] }}</option>
                                                @endforeach
                                            </optgroup>
                                        </select>
                                        <div class="form-text">
                                            <i class="fas fa-info-circle me-1"></i>{{ __('main.select_source_account') }}
                                        </div>
                                    </div>
                                </div>

                                <!-- To Account -->
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label fw-semibold text-dark mb-2">
                                            <i class="fas fa-arrow-down text-success me-1"></i>{{ __('main.to_account') }}
                                        </label>
                                        <select name="to_account_id" x-model="toAccountId" class="form-select form-select-lg" required>
                                            <option value="">{{ __('main.select_account') }}</option>
                                            <optgroup label="{{ __('main.banks') }}" class="fw-semibold">
                                                 @foreach($banksFormatted as $bank)
                                                    <option value="{{ $bank['id'] }}" :disabled="fromAccountId === '{{ $bank['id'] }}'">{{ $bank['name'] }}</option>
                                                @endforeach
                                            </optgroup>
                                            <optgroup label="{{ __('main.cash_boxes') }}" class="fw-semibold">
                                                @foreach($cashBoxesFormatted as $cashBox)
                                                    <option value="{{ $cashBox['id'] }}" :disabled="fromAccountId === '{{ $cashBox['id'] }}'">{{ $cashBox['name'] }}</option>
                                                @endforeach
                                            </optgroup>
                                        </select>
                                        <div class="form-text">
                                            <i class="fas fa-info-circle me-1"></i>{{ __('main.select_destination_account') }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Transfer Details Section -->
                            <div class="row g-4 mb-4">
                                <div class="col-12">
                                    <h6 class="text-primary fw-bold mb-3">
                                        <i class="fas fa-cog me-2"></i>{{ __('main.transfer_details') }}
                                    </h6>
                                </div>

                                <!-- Currency -->
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label fw-semibold text-dark mb-2">
                                            <i class="fas fa-coins text-warning me-1"></i>{{ __('main.currency') }}
                                        </label>
                                        <select name="currency_id" x-model="currencyId" class="form-select" required :disabled="!fromAccountId">
                                            <option value="">{{ __('main.select_currency') }}</option>
                                            @foreach($allCurrencies as $currency)
                                                <option value="{{ $currency->id }}">{{ $currency->name }} ({{ $currency->code }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Amount -->
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label fw-semibold text-dark mb-2">
                                            <i class="fas fa-dollar-sign text-success me-1"></i>{{ __('main.amount') }}
                                        </label>
                                        <div class="input-group">
                                            <input type="number" name="amount" x-model.number="amount" class="form-control" step="0.01" required :disabled="!currencyId" :max="fromAccountBalance" placeholder="0.00">
                                          
                                        </div>
                                    </div>
                                </div>

                                <!-- Date -->
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label fw-semibold text-dark mb-2">
                                            <i class="fas fa-calendar text-info me-1"></i>{{ __('main.date') }}
                                        </label>
                                        <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                                    </div>
                                </div>

                                <!-- Notes -->
                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="form-label fw-semibold text-dark mb-2">
                                            <i class="fas fa-sticky-note text-secondary me-1"></i>{{ __('main.notes') }}
                                        </label>
                                        <textarea name="notes" class="form-control" rows="3" placeholder="{{ __('main.transfer_notes_placeholder') }}"></textarea>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Submit Button -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                        <button type="submit" class="btn btn-primary btn-lg" :disabled="!fromAccountId || !toAccountId || !currencyId || !amount || amount <= 0 || amount > fromAccountBalance">
                                            <i class="fas fa-paper-plane me-2"></i>{{ __('main.execute_transfer') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Modern ERP Design Styles */
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    }
    
    .card {
        border-radius: 1rem;
        border: none;
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }
    
    .card-header {
        border-bottom: none;
    }
    
    .card-body {
        background: #fafbfc;
    }
    
    .form-select, .form-control {
        border-radius: 0.75rem;
        border: 2px solid #e9ecef;
        padding: 0.75rem 1rem;
        font-size: 1rem;
        transition: all 0.3s ease;
        background-color: #fff;
    }
    
    .form-select:focus, .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        outline: none;
    }
    
    .form-select-lg {
        font-size: 1.1rem;
        padding: 1rem 1.25rem;
    }
    
    .form-label {
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 0.5rem;
    }
    
    .btn {
        border-radius: 0.75rem;
        font-weight: 600;
        padding: 0.75rem 1.5rem;
        transition: all 0.3s ease;
        border: none;
    }
    
    .btn-lg {
        padding: 1rem 2rem;
        font-size: 1.1rem;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
    }
    
    .btn-outline-secondary {
        border: 2px solid #e9ecef;
        color: #6c757d;
    }
    
    .btn-outline-secondary:hover {
        background-color: #f8f9fa;
        border-color: #6c757d;
        color: #495057;
    }
    
    .alert {
        border-radius: 0.75rem;
        border: none;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    
    .input-group-text {
        border-radius: 0 0.75rem 0.75rem 0;
        border: 2px solid #e9ecef;
        border-left: none;
        background-color: #f8f9fa;
    }
    
    .form-text {
        font-size: 0.875rem;
        color: #6c757d;
    }
    
    .text-primary {
        color: #667eea !important;
    }
    
    .text-success {
        color: #48bb78 !important;
    }
    
    .text-danger {
        color: #f56565 !important;
    }
    
    .text-warning {
        color: #ed8936 !important;
    }
    
    .text-info {
        color: #4299e1 !important;
    }
    
    .text-secondary {
        color: #a0aec0 !important;
    }
    
    .text-muted {
        color: #718096 !important;
    }
    
    .text-dark {
        color: #2d3748 !important;
    }
    
    .shadow-lg {
        box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175) !important;
    }
    
    .shadow-sm {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
    }
    
    .bg-opacity-25 {
        background-color: rgba(255, 255, 255, 0.25) !important;
    }
    
    .rounded-circle {
        border-radius: 50% !important;
    }
    
    .p-2 {
        padding: 0.5rem !important;
    }
    
    .me-3 {
        margin-right: 1rem !important;
    }
    
    .me-2 {
        margin-right: 0.5rem !important;
    }
    
    .me-1 {
        margin-right: 0.25rem !important;
    }
    
    .mb-0 {
        margin-bottom: 0 !important;
    }
    
    .mb-2 {
        margin-bottom: 0.5rem !important;
    }
    
    .mb-3 {
        margin-bottom: 1rem !important;
    }
    
    .mb-4 {
        margin-bottom: 1.5rem !important;
    }
    
    .mt-1 {
        margin-top: 0.25rem !important;
    }
    
    .py-3 {
        padding-top: 1rem !important;
        padding-bottom: 1rem !important;
    }
    
    .py-4 {
        padding-top: 1.5rem !important;
        padding-bottom: 1.5rem !important;
    }
    
    .p-4 {
        padding: 1.5rem !important;
    }
    
    .p-lg-5 {
        padding: 3rem !important;
    }
    
    .fs-4 {
        font-size: 1.5rem !important;
    }
    
    .fs-5 {
        font-size: 1.25rem !important;
    }
    
    .fw-bold {
        font-weight: 700 !important;
    }
    
    .fw-semibold {
        font-weight: 600 !important;
    }
    
    .opacity-75 {
        opacity: 0.75 !important;
    }
    
    .d-flex {
        display: flex !important;
    }
    
    .align-items-center {
        align-items: center !important;
    }
    
    .justify-content-between {
        justify-content: space-between !important;
    }
    
    .justify-content-md-end {
        justify-content: flex-end !important;
    }
    
    .flex-shrink-0 {
        flex-shrink: 0 !important;
    }
    
    .flex-grow-1 {
        flex-grow: 1 !important;
    }
    
    .d-grid {
        display: grid !important;
    }
    
    .gap-2 {
        gap: 0.5rem !important;
    }
    
    .d-md-flex {
        display: flex !important;
    }
    
    .me-md-2 {
        margin-right: 0.5rem !important;
    }
    
    .ps-4 {
        padding-left: 1.5rem !important;
    }
    
    .g-4 {
        gap: 1.5rem !important;
    }
    
    .row {
        display: flex;
        flex-wrap: wrap;
        margin-right: -0.75rem;
        margin-left: -0.75rem;
    }
    
    .col-12, .col-lg-6 {
        position: relative;
        width: 100%;
        padding-right: 0.75rem;
        padding-left: 0.75rem;
    }
    
    .col-xl-8, .col-lg-10 {
        position: relative;
        width: 100%;
        padding-right: 0.75rem;
        padding-left: 0.75rem;
    }
    
    .container-fluid {
        width: 100%;
        padding-right: 1rem;
        padding-left: 1rem;
        margin-right: auto;
        margin-left: auto;
    }
    
    .justify-content-center {
        justify-content: center !important;
    }
    
    /* Responsive Design */
    @media (min-width: 992px) {
        .col-lg-6 {
            flex: 0 0 50%;
            max-width: 50%;
        }
        .col-lg-10 {
            flex: 0 0 83.333333%;
            max-width: 83.333333%;
        }
    }
    
    @media (min-width: 1200px) {
        .col-xl-8 {
            flex: 0 0 66.666667%;
            max-width: 66.666667%;
        }
    }
    
    @media (max-width: 991.98px) {
        .p-lg-5 {
            padding: 1.5rem !important;
        }
        .btn-lg {
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
        }
    }
    
    @media (max-width: 767.98px) {
        .d-md-flex {
            display: block !important;
        }
        .me-md-2 {
            margin-right: 0 !important;
            margin-bottom: 0.5rem !important;
        }
        .btn {
            width: 100%;
            margin-bottom: 0.5rem;
        }
    }
</style>
@endsection

@push('scripts')
<script>
    function transferForm() {
        return {
            accounts: {},
            fromAccountId: '{{ old('from_account_id') }}' || '',
            toAccountId: '{{ old('to_account_id') }}' || '',
            currencyId: '{{ old('currency_id') }}' || '',
            amount: '{{ old('amount') }}' || '',

            initData(accountsData) {
                this.accounts = accountsData.reduce((acc, account) => {
                    acc[account.id] = account;
                    return acc;
                }, {});
            },

            get fromAccount() {
                return this.accounts[this.fromAccountId] || null;
            },

            get toAccount() {
                return this.accounts[this.toAccountId] || null;
            },

            get fromAccountBalance() {
                if (!this.fromAccount || !this.currencyId) return 0;
                return this.fromAccount.currencies[this.currencyId]?.balance || 0;
            },

            get toAccountBalance() {
                if (!this.toAccount || !this.currencyId) return 0;
                return this.toAccount.currencies[this.currencyId]?.balance || 0;
            },

            get fromAccountBalanceText() {
                return this.formatBalance(this.fromAccountBalance);
            },

            get toAccountBalanceText() {
                return this.formatBalance(this.toAccountBalance);
            },

            formatBalance(value) {
                const num = parseFloat(value);
                if (isNaN(num)) return '0.00';
                return num.toFixed(2);
            }
        }
    }
</script>
@endpush
