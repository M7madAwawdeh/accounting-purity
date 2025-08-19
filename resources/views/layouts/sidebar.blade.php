@auth
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            @if(config('settings.logo'))
                <img src="{{ asset('storage/' . config('settings.logo')) }}" alt="Logo" class="sidebar-logo">
            @endif
            <h5>{{ config('settings.app_name', config('app.name', 'Laravel')) }}</h5>
        </div>
        <ul class="sidebar-menu">
            <li class="nav-item"><a class="nav-link {{ Request::is('home*') ? 'active' : '' }}" href="{{ route('home') }}"><i class="fas fa-home"></i> {{ __('main.dashboard') }}</a></li>
            <li class="nav-item"><a class="nav-link {{ Request::is('financial-reports*') ? 'active' : '' }}" href="{{ route('financial-reports.index') }}"><i class="fas fa-chart-line"></i> {{ __('main.financial_reports') }}</a></li>
            <li class="nav-item"><a class="nav-link {{ Request::is('account-report*') ? 'active' : '' }}" href="{{ route('account-report.index') }}"><i class="fas fa-file-invoice"></i> {{ __('main.account_statement') }}</a></li>
            
            <li class="nav-item-header">{{ __('main.accounts') }}</li>
            <li class="nav-item"><a class="nav-link {{ Request::is('cash-boxes*') ? 'active' : '' }}" href="{{ route('cash-boxes.index') }}"><i class="fas fa-cash-register"></i> {{ __('main.cash_boxes') }}</a></li>
            <li class="nav-item"><a class="nav-link {{ Request::is('banks*') ? 'active' : '' }}" href="{{ route('banks.index') }}"><i class="fas fa-university"></i> {{ __('main.banks') }}</a></li>
            <li class="nav-item"><a class="nav-link {{ Request::is('cheques*') ? 'active' : '' }}" href="{{ route('cheques.index') }}"><i class="fas fa-money-check-alt"></i> {{ __('main.cheques') }}</a></li>
            <li class="nav-item"><a class="nav-link {{ Request::is('account-transfers*') ? 'active' : '' }}" href="{{ route('account-transfers.create') }}"><i class="fas fa-exchange-alt"></i> {{ __('main.transfer_funds') }}</a></li>

            <li class="nav-item-header">{{ __('main.transactions') }}</li>
            <li class="nav-item"><a class="nav-link {{ Request::is('donations*') ? 'active' : '' }}" href="{{ route('donations.index') }}"><i class="fas fa-hand-holding-usd"></i> {{ __('main.donations') }}</a></li>
            <li class="nav-item"><a class="nav-link {{ Request::is('expense-vouchers*') ? 'active' : '' }}" href="{{ route('expense-vouchers.index') }}"><i class="fas fa-file-invoice-dollar"></i> {{ __('main.expense_vouchers') }}</a></li>
            <li class="nav-item"><a class="nav-link {{ Request::is('payment-vouchers*') ? 'active' : '' }}" href="{{ route('payment-vouchers.index') }}"><i class="fas fa-receipt"></i> {{ __('main.payment_vouchers') }}</a></li>
            
            <li class="nav-item-header">{{ __('main.management') }}</li>
            <li class="nav-item"><a class="nav-link {{ Request::is('employees*') ? 'active' : '' }}" href="{{ route('employees.index') }}"><i class="fas fa-users"></i> {{ __('main.employees') }}</a></li>
            <li class="nav-item"><a class="nav-link {{ Request::is('suppliers*') ? 'active' : '' }}" href="{{ route('suppliers.index') }}"><i class="fas fa-truck"></i> {{ __('main.suppliers') }}</a></li>
            <li class="nav-item"><a class="nav-link {{ Request::is('members*') ? 'active' : '' }}" href="{{ route('members.index') }}"><i class="fas fa-user-friends"></i> {{ __('main.members') }}</a></li>
            <li class="nav-item"><a class="nav-link {{ Request::is('funders*') ? 'active' : '' }}" href="{{ route('funders.index') }}"><i class="fas fa-hands-helping"></i> {{ __('main.funders') }}</a></li>
            <li class="nav-item"><a class="nav-link {{ Request::is('projects*') ? 'active' : '' }}" href="{{ route('projects.index') }}"><i class="fas fa-project-diagram"></i> {{ __('main.projects') }}</a></li>
            
            <li class="nav-item-header">{{ __('main.system') }}</li>
            <li class="nav-item"><a class="nav-link {{ Request::is('currencies*') ? 'active' : '' }}" href="{{ route('currencies.index') }}"><i class="fas fa-money-bill-wave"></i> {{ __('main.currencies') }}</a></li>
            <li class="nav-item"><a class="nav-link {{ Request::is('settings*') ? 'active' : '' }}" href="{{ route('settings.index') }}"><i class="fas fa-cog"></i> {{ __('main.settings') }}</a></li>
        </ul>
        <div class="sidebar-footer">
            <div class="nav-item dropdown">
                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                    <i class="fas fa-user-circle"></i> {{ Auth::user()->name }}
                </a>
                <div class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="{{ route('logout') }}"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i> {{ __('main.logout') }}
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>
@endauth 