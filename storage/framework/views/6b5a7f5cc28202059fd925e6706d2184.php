

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?php echo e(__('main.expense_vouchers')); ?></h1>
        <a href="<?php echo e(route('expense-vouchers.create')); ?>" class="btn btn-primary"><?php echo e(__('main.add_expense_voucher')); ?></a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h5 class="mb-0"><?php echo e(__('main.filter')); ?></h5>
        </div>
        <div class="card-body">
            <form method="GET" action="<?php echo e(route('expense-vouchers.index')); ?>" class="form-inline">
                <div class="form-group mb-2 mr-sm-2">
                    <label for="search" class="sr-only"><?php echo e(__('main.search')); ?></label>
                    <input type="text" name="search" id="search" class="form-control" placeholder="<?php echo e(__('main.search_by_recipient_or_desc')); ?>" value="<?php echo e(request('search')); ?>">
                </div>
                <div class="form-group mb-2 mr-sm-2">
                    <label for="project_id" class="sr-only"><?php echo e(__('main.project')); ?></label>
                    <select name="project_id" id="project_id" class="form-control">
                        <option value=""><?php echo e(__('main.all_projects')); ?></option>
                        <?php $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($project->id); ?>" <?php echo e(request('project_id') == $project->id ? 'selected' : ''); ?>><?php echo e($project->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-secondary mb-2"><?php echo e(__('main.search')); ?></button>
                <a href="<?php echo e(route('expense-vouchers.index')); ?>" class="btn btn-light mb-2 ml-2"><?php echo e(__('main.reset')); ?></a>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive-md">
                <table class="table table-striped table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th><?php echo e(__('main.date')); ?></th>
                            <th><?php echo e(__('main.recipient')); ?></th>
                            <th><?php echo e(__('main.amount')); ?></th>
                            <th><?php echo e(__('main.payment_method')); ?></th>
                            <th><?php echo e(__('main.project')); ?></th>
                            <th><?php echo e(__('main.actions')); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $expenseVouchers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $voucher): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($voucher->id); ?></td>
                                <td><?php echo e($voucher->date); ?></td>
                                <td><?php echo e($voucher->recipient); ?></td>
                                <td><?php echo e(number_format($voucher->amount, 2)); ?> <?php echo e(getCurrencyName($voucher->currency, true)); ?></td>
                                <td><?php echo e(__('main.payment_method_' . $voucher->payment_method)); ?></td>
                                <td>
                                    <?php if($voucher->project): ?>
                                        <a href="<?php echo e(route('projects.show', $voucher->project_id)); ?>" class="btn btn-sm btn-outline-info" target="_blank"><?php echo e($voucher->project->name); ?></a>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?php echo e(route('expense-vouchers.show', $voucher)); ?>" class="btn btn-sm btn-info"><?php echo e(__('main.show')); ?></a>
                                    <a href="<?php echo e(route('expense-vouchers.edit', $voucher)); ?>" class="btn btn-sm btn-primary"><?php echo e(__('main.edit')); ?></a>
                                    <a href="<?php echo e(route('expense-vouchers.show', ['expense_voucher' => $voucher->id, 'print' => true])); ?>" target="_blank" class="btn btn-sm btn-secondary"><?php echo e(__('main.print')); ?></a>
                                    <form action="<?php echo e(route('expense-vouchers.destroy', $voucher)); ?>" method="POST" style="display:inline-block;">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-sm btn-danger delete-btn"><?php echo e(__('main.delete')); ?></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="text-center"><?php echo e(__('main.no_expense_vouchers_found')); ?></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center">
                <?php echo e($expenseVouchers->appends(request()->query())->links()); ?>

            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\mohja\Desktop\projects\fintks-accounting\resources\views/expense-vouchers/index.blade.php ENDPATH**/ ?>