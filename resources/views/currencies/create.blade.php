@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-plus-circle text-primary mr-2"></i>
            {{ __('main.add_currency') }}
        </h1>
        <a href="{{ route('currencies.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left mr-1"></i>
            {{ __('main.back_to_list') }}
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-money-bill-wave mr-2"></i>
                        {{ __('main.currency_information') }}
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('currencies.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label fw-bold text-dark">
                                    <i class="fas fa-tag text-primary mr-1"></i>
                                    {{ __('main.name') }}
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       name="name" 
                                       id="name" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name') }}" 
                                       placeholder="{{ __('main.enter_currency_name') }}"
                                       required />
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="code" class="form-label fw-bold text-dark">
                                    <i class="fas fa-code text-primary mr-1"></i>
                                    {{ __('main.code') }}
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       name="code" 
                                       id="code" 
                                       class="form-control @error('code') is-invalid @enderror" 
                                       value="{{ old('code') }}" 
                                       placeholder="USD, EUR, ILS..."
                                       maxlength="3"
                                       style="text-transform: uppercase;"
                                       required />
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="symbol" class="form-label fw-bold text-dark">
                                    <i class="fas fa-dollar-sign text-primary mr-1"></i>
                                    {{ __('main.symbol') }}
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       name="symbol" 
                                       id="symbol" 
                                       class="form-control @error('symbol') is-invalid @enderror" 
                                       value="{{ old('symbol') }}" 
                                       placeholder="$, €, ₪..."
                                       maxlength="5"
                                       required />
                                @error('symbol')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="exchange_rate" class="form-label fw-bold text-dark">
                                    <i class="fas fa-exchange-alt text-primary mr-1"></i>
                                    {{ __('main.exchange_rate') }}
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" 
                                           name="exchange_rate" 
                                           id="exchange_rate" 
                                           class="form-control @error('exchange_rate') is-invalid @enderror" 
                                           value="{{ old('exchange_rate', 1) }}" 
                                           step="0.0001" 
                                           min="0"
                                           placeholder="1.0000"
                                           required />
                                    <span class="input-group-text">USD</span>
                                </div>
                                <small class="form-text text-muted">
                                    {{ __('main.exchange_rate_help') }}
                                </small>
                                @error('exchange_rate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 mb-4">
                                <div class="form-check">
                                    <input type="hidden" name="is_default" value="0">
                                    <input class="form-check-input @error('is_default') is-invalid @enderror" 
                                           type="checkbox" 
                                           name="is_default" 
                                           value="1" 
                                           id="is_default"
                                           {{ old('is_default') ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold text-dark" for="is_default">
                                        <i class="fas fa-star text-warning mr-1"></i>
                                        {{ __('main.is_default') }}
                                    </label>
                                    <small class="form-text text-muted d-block">
                                        {{ __('main.default_currency_help') }}
                                    </small>
                                    @error('is_default')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-outline-secondary" onclick="history.back()">
                                <i class="fas fa-times mr-1"></i>
                                {{ __('main.cancel') }}
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-1"></i>
                                {{ __('main.add_currency') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.form-control:focus {
    border-color: #4e73df;
    box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
}

.form-check-input:checked {
    background-color: #4e73df;
    border-color: #4e73df;
}

.card {
    border: none;
    border-radius: 0.75rem;
}

.card-header {
    border-radius: 0.75rem 0.75rem 0 0 !important;
}

@media (max-width: 768px) {
    .container-fluid {
        padding: 1rem;
    }
    
    .card-body {
        padding: 1.5rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-uppercase currency code
    const codeInput = document.getElementById('code');
    codeInput.addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });
    
    // Auto-format exchange rate
    const rateInput = document.getElementById('exchange_rate');
    rateInput.addEventListener('blur', function() {
        if (this.value) {
            this.value = parseFloat(this.value).toFixed(4);
        }
    });
});
</script>
@endsection