

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?php echo e(__('main.projects')); ?></h1>
        <a href="<?php echo e(route('projects.create')); ?>" class="btn btn-primary"><?php echo e(__('main.add_project')); ?></a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="<?php echo e(route('projects.index')); ?>" class="form-inline">
                <div class="form-group mb-2 mr-sm-2">
                    <label for="search" class="sr-only"><?php echo e(__('main.search')); ?></label>
                    <input type="text" name="search" id="search" class="form-control" placeholder="<?php echo e(__('main.search_by_project_or_funder')); ?>" value="<?php echo e(request('search')); ?>">
                </div>
                <button type="submit" class="btn btn-secondary mb-2"><?php echo e(__('main.search')); ?></button>
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
                            <th><?php echo e(__('main.name')); ?></th>
                            <th><?php echo e(__('main.funder')); ?></th>
                            <th><?php echo e(__('main.start_date')); ?></th>
                            <th><?php echo e(__('main.end_date')); ?></th>
                            <th><?php echo e(__('main.actions')); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($project->id); ?></td>
                                <td><?php echo e($project->name); ?></td>
                                <td><?php echo e($project->funder->name ?? ''); ?></td>
                                <td><?php echo e($project->start_date); ?></td>
                                <td><?php echo e($project->end_date); ?></td>
                                <td>
                                    <a href="<?php echo e(route('projects.show', $project->id)); ?>" class="btn btn-sm btn-info"><?php echo e(__('main.show')); ?></a>
                                    <a href="<?php echo e(route('projects.edit', $project->id)); ?>" class="btn btn-sm btn-primary"><?php echo e(__('main.edit')); ?></a>
                                    <a href="<?php echo e(route('projects.show', ['project' => $project->id, 'print' => true])); ?>" target="_blank" class="btn btn-sm btn-secondary"><?php echo e(__('main.print')); ?></a>
                                    <form action="<?php echo e(route('projects.destroy', $project->id)); ?>" method="POST" style="display:inline-block;">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-sm btn-danger delete-btn"><?php echo e(__('main.delete')); ?></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="text-center"><?php echo e(__('main.no_projects_found')); ?></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
             <div class="d-flex justify-content-center">
                <?php echo e($projects->appends(request()->query())->links()); ?>

            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\mohja\Desktop\projects\fintks-accounting\resources\views/projects/index.blade.php ENDPATH**/ ?>