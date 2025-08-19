<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?php echo e(__('main.dashboard')); ?></h1>
    </div>

    <!-- Currency Totals Row -->
    <?php $__currentLoopData = collect($currencyTotals)->chunk(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $chunk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="row">
        <?php $__currentLoopData = $chunk; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $currency => $totals): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                <?php echo e(__('main.net_balance')); ?> (<?php echo e($currency); ?>)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e(number_format($totals['net'], 2)); ?> <?php echo e($totals['symbol']); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-balance-scale fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


    <div class="row">
         <div class="col-xl-3 col-md-6 mb-4">
            <a href="<?php echo e(route('projects.index')); ?>" class="text-decoration-none">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1"><?php echo e(__('main.projects')); ?></div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($stats['projects_count']); ?></div>
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
             <a href="<?php echo e(route('funders.index')); ?>" class="text-decoration-none">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1"><?php echo e(__('main.funders')); ?></div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($stats['funders_count']); ?></div>
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
                    <h5 class="mb-0"><?php echo e(__('main.monthly_summary')); ?> (USD)</h5>
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
                <div class="card-header"><h6 class="m-0 font-weight-bold text-primary"><?php echo e(__('main.recent_donations')); ?></h6></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                             <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $recentDonations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $donation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($donation->date); ?></td>
                                        <td><?php echo e($donation->donor_name); ?></td>
                                        <td class="text-right"><?php echo e(number_format($donation->amount, 2)); ?> <?php echo e(getCurrencyName($donation->currency, true)); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr><td class="text-center"><?php echo e(__('main.no_recent_donations')); ?></td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
         <div class="col-lg-4">
            <div class="card shadow mb-4">
                 <div class="card-header"><h6 class="m-0 font-weight-bold text-primary"><?php echo e(__('main.recent_payment_vouchers')); ?></h6></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                             <tbody>
                                 <?php $__empty_1 = true; $__currentLoopData = $recentPaymentVouchers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $voucher): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($voucher->date); ?></td>
                                        <td><?php echo e($voucher->payer); ?></td>
                                        <td class="text-right"><?php echo e(number_format($voucher->amount, 2)); ?> <?php echo e(getCurrencyName($voucher->currency, true)); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr><td class="text-center"><?php echo e(__('main.no_recent_payment_vouchers')); ?></td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
         <div class="col-lg-4">
            <div class="card shadow mb-4">
                 <div class="card-header"><h6 class="m-0 font-weight-bold text-primary"><?php echo e(__('main.recent_expenses')); ?></h6></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <tbody>
                                 <?php $__empty_1 = true; $__currentLoopData = $recentExpenses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $expense): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($expense->date); ?></td>
                                        <td><?php echo e($expense->recipient); ?></td>
                                        <td class="text-right text-danger"><?php echo e(number_format($expense->amount, 2)); ?> <?php echo e(getCurrencyName($expense->currency, true)); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr><td class="text-center"><?php echo e(__('main.no_recent_expenses')); ?></td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('financialChart').getContext('2d');
    const financialChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($monthlyData['months'], 15, 512) ?>,
            datasets: [{
                label: '<?php echo e(__("main.income")); ?>',
                data: <?php echo json_encode($monthlyData['income'], 15, 512) ?>,
                backgroundColor: 'rgba(40, 167, 69, 0.7)',
                borderColor: 'rgba(40, 167, 69, 1)',
                borderWidth: 1
            }, {
                label: '<?php echo e(__("main.expenses")); ?>',
                data: <?php echo json_encode($monthlyData['expenses'], 15, 512) ?>,
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
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\mohja\Desktop\projects\fintks-accounting\resources\views/home.blade.php ENDPATH**/ ?>