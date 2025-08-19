@extends('layouts.app')

@section('content')
<style>
    body {
        background-color: #f8f9fc;
    }
    .card-login {
        border: none;
        border-radius: 1rem;
        box-shadow: 0 0.5rem 1rem 0 rgba(0, 0, 0, 0.1);
    }
    .card-login .card-header {
        background-color: transparent;
        border-bottom: none;
        padding-top: 2rem;
        padding-bottom: 1rem;
    }
    .card-login .card-header .logo {
        font-size: 1.5rem;
        font-weight: bold;
    }
    .form-control-user {
        border-radius: 10rem;
        padding: 1.5rem 1rem;
        font-size: 0.8rem;
    }
    .btn-user {
        font-size: 0.8rem;
        border-radius: 10rem;
        padding: 0.75rem 1rem;
    }
</style>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-xl-6 col-lg-7 col-md-9">
            <div class="card o-hidden card-login my-5">
                <div class="card-body p-0">
                    <div class="p-5">
                        <div class="text-center">
                             @if(config('settings.logo'))
                                <img src="{{ asset('storage/' . config('settings.logo')) }}" alt="Logo" class="mb-4" style="max-height: 70px;">
                            @endif
                            <h1 class="h4 text-gray-900 mb-4">{{ __('main.welcome_back') }}</h1>
                        </div>
                        <form method="POST" action="{{ route('login') }}" class="user">
                            @csrf
                            <div class="form-group">
                                <input id="email" type="email" class="form-control form-control-user @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="{{ __('main.email_address') }}">
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <input id="password" type="password" class="form-control form-control-user @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="{{ __('main.password') }}">
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <div class="custom-control custom-checkbox small">
                                    <input class="custom-control-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="remember">
                                        {{ __('main.remember_me') }}
                                    </label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-user btn-block">
                                {{ __('main.login') }}
                            </button>
                        </form>
                        <hr>
                        <div class="text-center">
                             @if (Route::has('password.request'))
                                <a class="small" href="{{ route('password.request') }}">
                                    {{ __('main.forgot_your_password') }}
                                </a>
                            @endif
                        </div>
                         {{-- You can add a registration link here if needed --}}
                         {{-- <div class="text-center">
                            <a class="small" href="{{ route('register') }}">{{ __('main.create_an_account') }}</a>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
