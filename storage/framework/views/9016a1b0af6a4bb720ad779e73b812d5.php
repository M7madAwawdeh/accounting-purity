

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?php echo e(__('main.currencies')); ?></h1>
        <a href="<?php echo e(route('currencies.create')); ?>" class="btn btn-primary"><?php echo e(__('main.add_currency')); ?></a>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th><?php echo e(__('main.name')); ?></th>
                            <th><?php echo e(__('main.code')); ?></th>
                            <th><?php echo e(__('main.symbol')); ?></th>
                            <th><?php echo e(__('main.exchange_rate')); ?></th>
                            <th><?php echo e(__('main.is_default')); ?></th>
                            <th class="text-center"><?php echo e(__('main.actions')); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $currencies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $currency): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($currency->name); ?></td>
                                <td><?php echo e($currency->code); ?></td>
                                <td><?php echo e($currency->symbol); ?></td>
                                <td><?php echo e($currency->exchange_rate); ?></td>
                                <td>
                                    <?php if($currency->is_default): ?>
                                        <span class="badge bg-success"><?php echo e(__('main.yes')); ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary"><?php echo e(__('main.no')); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <a href="<?php echo e(route('currencies.edit', $currency->id)); ?>" class="btn btn-sm btn-outline-primary"><?php echo e(__('main.edit')); ?></a>
                                    <form action="<?php echo e(route('currencies.destroy', $currency->id)); ?>" method="POST" class="d-inline" onsubmit="return confirm('<?php echo e(__('main.are_you_sure_delete')); ?>');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger"><?php echo e(__('main.delete')); ?></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="text-center"><?php echo e(__('main.no_currencies_found')); ?></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\mohja\Desktop\projects\fintks-accounting\resources\views/currencies/index.blade.php ENDPATH**/ ?>