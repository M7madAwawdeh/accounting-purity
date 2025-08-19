@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('main.dashboard') }}</h1>
    </div>

    <!-- Currency Totals Row -->
    @foreach(collect($currencyTotals)->chunk(3) as $chunk)
    <div class="row">
        @foreach($chunk as $currency => $totals)
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                {{ __('main.net_balance') }} ({{ $currency }})</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totals['net'], 2) }} {{ $totals['symbol'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-balance-scale fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endforeach


    <div class="row">
         <div class="col-xl-3 col-md-6 mb-4">
            <a href="{{ route('projects.index') }}" class="text-decoration-none">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">{{ __('main.projects') }}</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['projects_count'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-project-diagram fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
             <a href="{{ route('funders.index') }}" class="text-decoration-none">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">{{ __('main.funders') }}</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['funders_count'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-hands-helping fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>


    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('main.monthly_summary') }} (USD)</h5>
                </div>
                <div class="card-body">
                    <canvas id="financialChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header"><h6 class="m-0 font-weight-bold text-primary">{{ __('main.recent_donations') }}</h6></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                             <tbody>
                                @forelse($recentDonations as $donation)
                                    <tr>
                                        <td>{{ $donation->date }}</td>
                                        <td>{{ $donation->donor_name }}</td>
                                        <td class="text-right">{{ number_format($donation->amount, 2) }} {{ getCurrencyName($donation->currency, true) }}</td>
                                    </tr>
                                @empty
                                    <tr><td class="text-center">{{ __('main.no_recent_donations') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
         <div class="col-lg-4">
            <div class="card shadow mb-4">
                 <div class="card-header"><h6 class="m-0 font-weight-bold text-primary">{{ __('main.recent_payment_vouchers') }}</h6></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                             <tbody>
                                 @forelse($recentPaymentVouchers as $voucher)
                                    <tr>
                                        <td>{{ $voucher->date }}</td>
                                        <td>{{ $voucher->payer }}</td>
                                        <td class="text-right">{{ number_format($voucher->amount, 2) }} {{ getCurrencyName($voucher->currency, true) }}</td>
                                    </tr>
                                @empty
                                    <tr><td class="text-center">{{ __('main.no_recent_payment_vouchers') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
         <div class="col-lg-4">
            <div class="card shadow mb-4">
                 <div class="card-header"><h6 class="m-0 font-weight-bold text-primary">{{ __('main.recent_expenses') }}</h6></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <tbody>
                                 @forelse($recentExpenses as $expense)
                                    <tr>
                                        <td>{{ $expense->date }}</td>
                                        <td>{{ $expense->recipient }}</td>
                                        <td class="text-right text-danger">{{ number_format($expense->amount, 2) }} {{ getCurrencyName($expense->currency, true) }}</td>
                                    </tr>
                                @empty
                                    <tr><td class="text-center">{{ __('main.no_recent_expenses') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('financialChart').getContext('2d');
    const financialChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($monthlyData['months']),
            datasets: [{
                label: '{{ __("main.income") }}',
                data: @json($monthlyData['income']),
                backgroundColor: 'rgba(40, 167, 69, 0.7)',
                borderColor: 'rgba(40, 167, 69, 1)',
                borderWidth: 1
            }, {
                label: '{{ __("main.expenses") }}',
                data: @json($monthlyData['expenses']),
                backgroundColor: 'rgba(220, 53, 69, 0.7)',
                borderColor: 'rgba(220, 53, 69, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value, index, values) {
                            return new Intl.NumberFormat().format(value);
                        }
                    }
                }
            }
        }
    });
</script>
@endpush
