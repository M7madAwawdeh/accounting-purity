

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?php echo e(__('main.cash_box_details')); ?></h1>
        <div>
            <a href="<?php echo e(route('cash-boxes.index')); ?>" class="btn btn-secondary btn-sm"><?php echo e(__('main.back_to_list')); ?></a>
            <a href="<?php echo e(route('cash-boxes.edit', $cashBox->id)); ?>" class="btn btn-primary btn-sm"><?php echo e(__('main.edit')); ?></a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><?php echo e($cashBox->name); ?></h6>
                </div>
                <div class="card-body">
                    <?php if($cashBox->location): ?>
                        <p><strong><?php echo e(__('main.location')); ?>:</strong> <?php echo e($cashBox->location); ?></p>
                    <?php endif; ?>
                    <?php if($cashBox->manager): ?>
                        <p><strong><?php echo e(__('main.manager')); ?>:</strong> <?php echo e($cashBox->manager); ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><?php echo e(__('main.currency_balances')); ?></h6>
                </div>
                <div class="card-body">
                    <?php $__empty_1 = true; $__currentLoopData = $cashBox->currencies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $currency): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <h5><?php echo e($currency->name); ?>: <?php echo e(number_format($currency->pivot->balance, 2)); ?> <?php echo e($currency->symbol); ?></h5>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p><?php echo e(__('main.no_currencies_assigned')); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><?php echo e(__('main.transactions_history')); ?></h6>
                </div>
                <div class="card-body">
                     <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                            <thead class="thead-dark">
                                <tr>
                                    <th><?php echo e(__('main.date')); ?></th>
                                    <th><?php echo e(__('main.type')); ?></th>
                                    <th><?php echo e(__('main.details')); ?></th>
                                    <th><?php echo e(__('main.credit')); ?></th>
                                    <th><?php echo e(__('main.debit')); ?></th>
                                    <th><?php echo e(__('main.currency')); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($transaction->date); ?></td>
                                        <td><?php echo e($transaction->transaction_type); ?></td>
                                        <td><?php echo e($transaction->transaction_details); ?></td>
                                        <td class="text-success"><?php echo e($transaction->credit > 0 ? number_format($transaction->credit, 2) : '-'); ?></td>
                                        <td class="text-danger"><?php echo e($transaction->debit > 0 ? number_format($transaction->debit, 2) : '-'); ?></td>
                                        <td><?php echo e(getCurrencyName($transaction->currency, true)); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="6" class="text-center"><?php echo e(__('main.no_transactions_found')); ?></td>
                                    </tr>
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
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\mohja\Desktop\projects\fintks-accounting\resources\views/cash-boxes/show.blade.php ENDPATH**/ ?>