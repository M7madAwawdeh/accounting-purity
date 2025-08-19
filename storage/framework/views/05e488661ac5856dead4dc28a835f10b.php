<?php $__env->startSection('content'); ?>
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
                             <?php if(config('settings.logo')): ?>
                                <img src="<?php echo e(asset('storage/' . config('settings.logo'))); ?>" alt="Logo" class="mb-4" style="max-height: 70px;">
                            <?php endif; ?>
                            <h1 class="h4 text-gray-900 mb-4"><?php echo e(__('main.welcome_back')); ?></h1>
                        </div>
                        <form method="POST" action="<?php echo e(route('login')); ?>" class="user">
                            <?php echo csrf_field(); ?>
                            <div class="form-group">
                                <input id="email" type="email" class="form-control form-control-user <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="email" value="<?php echo e(old('email')); ?>" required autocomplete="email" autofocus placeholder="<?php echo e(__('main.email_address')); ?>">
                                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="invalid-feedback" role="alert">
                                        <strong><?php echo e($message); ?></strong>
                                    </span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="form-group">
                                <input id="password" type="password" class="form-control form-control-user <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="password" required autocomplete="current-password" placeholder="<?php echo e(__('main.password')); ?>">
                                <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="invalid-feedback" role="alert">
                                        <strong><?php echo e($message); ?></strong>
                                    </span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="form-group">
                                <div class="custom-control custom-checkbox small">
                                    <input class="custom-control-input" type="checkbox" name="remember" id="remember" <?php echo e(old('remember') ? 'checked' : ''); ?>>
                                    <label class="custom-control-label" for="remember">
                                        <?php echo e(__('main.remember_me')); ?>

                                    </label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-user btn-block">
                                <?php echo e(__('main.login')); ?>

                            </button>
                        </form>
                        <hr>
                        <div class="text-center">
                             <?php if(Route::has('password.request')): ?>
                                <a class="small" href="<?php echo e(route('password.request')); ?>">
                                    <?php echo e(__('main.forgot_your_password')); ?>

                                </a>
                            <?php endif; ?>
                        </div>
                         
                         
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\mohja\Desktop\projects\fintks-accounting\resources\views/auth/login.blade.php ENDPATH**/ ?>