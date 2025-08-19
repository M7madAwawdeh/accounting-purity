

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?php echo e(__('main.banks')); ?></h1>
        <a href="<?php echo e(route('banks.create')); ?>" class="btn btn-primary btn-sm shadow-sm"><?php echo e(__('main.add_bank')); ?></a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><?php echo e(__('main.search')); ?></h6>
        </div>
        <div class="card-body">
            <form action="<?php echo e(route('banks.index')); ?>" method="GET">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="<?php echo e(__('main.search_by_name_or_account')); ?>" value="<?php echo e(request('search')); ?>">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-outline-secondary"><?php echo e(__('main.search')); ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><?php echo e(__('main.banks_list')); ?></h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th><?php echo e(__('main.name')); ?></th>
                            <th><?php echo e(__('main.account_number')); ?></th>
                            <th><?php echo e(__('main.currencies_and_balances')); ?></th>
                            <th class="text-center"><?php echo e(__('main.actions')); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $banks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bank): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><a href="<?php echo e(route('banks.show', $bank->id)); ?>"><?php echo e($bank->name); ?></a></td>
                                <td><?php echo e($bank->account_number); ?></td>
                                <td>
                                    <?php $__currentLoopData = $bank->currencies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $currency): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <span class="badge badge-pill badge-info mr-1">
                                            <?php echo e($currency->name); ?>: <?php echo e(number_format($currency->pivot->balance, 2)); ?> <?php echo e($currency->symbol); ?>

                                        </span>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </td>
                                <td class="text-center">
                                    <a href="<?php echo e(route('banks.show', $bank->id)); ?>" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                    <a href="<?php echo e(route('banks.edit', $bank->id)); ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                                    <form action="<?php echo e(route('banks.destroy', $bank->id)); ?>" method="POST" style="display:inline-block;">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('<?php echo e(__('main.are_you_sure')); ?>')"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="4" class="text-center"><?php echo e(__('main.no_banks_found')); ?></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-3">
                <?php echo e($banks->links()); ?>

            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\mohja\Desktop\projects\fintks-accounting\resources\views/banks/index.blade.php ENDPATH**/ ?>