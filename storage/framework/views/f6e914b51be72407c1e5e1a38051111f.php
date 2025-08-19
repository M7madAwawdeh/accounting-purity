<?php if(auth()->guard()->check()): ?>
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <?php if(config('settings.logo')): ?>
                <img src="<?php echo e(asset('storage/' . config('settings.logo'))); ?>" alt="Logo" class="sidebar-logo">
            <?php endif; ?>
            <h5><?php echo e(config('settings.app_name', config('app.name', 'Laravel'))); ?></h5>
        </div>
        <ul class="sidebar-menu">
            <li class="nav-item"><a class="nav-link <?php echo e(Request::is('home*') ? 'active' : ''); ?>" href="<?php echo e(route('home')); ?>"><i class="fas fa-home"></i> <?php echo e(__('main.dashboard')); ?></a></li>
            <li class="nav-item"><a class="nav-link <?php echo e(Request::is('financial-reports*') ? 'active' : ''); ?>" href="<?php echo e(route('financial-reports.index')); ?>"><i class="fas fa-chart-line"></i> <?php echo e(__('main.financial_reports')); ?></a></li>
            <li class="nav-item"><a class="nav-link <?php echo e(Request::is('account-report*') ? 'active' : ''); ?>" href="<?php echo e(route('account-report.index')); ?>"><i class="fas fa-file-invoice"></i> <?php echo e(__('main.account_statement')); ?></a></li>
            
            <li class="nav-item-header"><?php echo e(__('main.accounts')); ?></li>
            <li class="nav-item"><a class="nav-link <?php echo e(Request::is('cash-boxes*') ? 'active' : ''); ?>" href="<?php echo e(route('cash-boxes.index')); ?>"><i class="fas fa-cash-register"></i> <?php echo e(__('main.cash_boxes')); ?></a></li>
            <li class="nav-item"><a class="nav-link <?php echo e(Request::is('banks*') ? 'active' : ''); ?>" href="<?php echo e(route('banks.index')); ?>"><i class="fas fa-university"></i> <?php echo e(__('main.banks')); ?></a></li>
            <li class="nav-item"><a class="nav-link <?php echo e(Request::is('cheques*') ? 'active' : ''); ?>" href="<?php echo e(route('cheques.index')); ?>"><i class="fas fa-money-check-alt"></i> <?php echo e(__('main.cheques')); ?></a></li>
            <li class="nav-item"><a class="nav-link <?php echo e(Request::is('account-transfers*') ? 'active' : ''); ?>" href="<?php echo e(route('account-transfers.create')); ?>"><i class="fas fa-exchange-alt"></i> <?php echo e(__('main.transfer_funds')); ?></a></li>

            <li class="nav-item-header"><?php echo e(__('main.transactions')); ?></li>
            <li class="nav-item"><a class="nav-link <?php echo e(Request::is('donations*') ? 'active' : ''); ?>" href="<?php echo e(route('donations.index')); ?>"><i class="fas fa-hand-holding-usd"></i> <?php echo e(__('main.donations')); ?></a></li>
            <li class="nav-item"><a class="nav-link <?php echo e(Request::is('expense-vouchers*') ? 'active' : ''); ?>" href="<?php echo e(route('expense-vouchers.index')); ?>"><i class="fas fa-file-invoice-dollar"></i> <?php echo e(__('main.expense_vouchers')); ?></a></li>
            <li class="nav-item"><a class="nav-link <?php echo e(Request::is('payment-vouchers*') ? 'active' : ''); ?>" href="<?php echo e(route('payment-vouchers.index')); ?>"><i class="fas fa-receipt"></i> <?php echo e(__('main.payment_vouchers')); ?></a></li>
            
            <li class="nav-item-header"><?php echo e(__('main.management')); ?></li>
            <li class="nav-item"><a class="nav-link <?php echo e(Request::is('employees*') ? 'active' : ''); ?>" href="<?php echo e(route('employees.index')); ?>"><i class="fas fa-users"></i> <?php echo e(__('main.employees')); ?></a></li>
            <li class="nav-item"><a class="nav-link <?php echo e(Request::is('suppliers*') ? 'active' : ''); ?>" href="<?php echo e(route('suppliers.index')); ?>"><i class="fas fa-truck"></i> <?php echo e(__('main.suppliers')); ?></a></li>
            <li class="nav-item"><a class="nav-link <?php echo e(Request::is('members*') ? 'active' : ''); ?>" href="<?php echo e(route('members.index')); ?>"><i class="fas fa-user-friends"></i> <?php echo e(__('main.members')); ?></a></li>
            <li class="nav-item"><a class="nav-link <?php echo e(Request::is('funders*') ? 'active' : ''); ?>" href="<?php echo e(route('funders.index')); ?>"><i class="fas fa-hands-helping"></i> <?php echo e(__('main.funders')); ?></a></li>
            <li class="nav-item"><a class="nav-link <?php echo e(Request::is('projects*') ? 'active' : ''); ?>" href="<?php echo e(route('projects.index')); ?>"><i class="fas fa-project-diagram"></i> <?php echo e(__('main.projects')); ?></a></li>
            
            <li class="nav-item-header"><?php echo e(__('main.system')); ?></li>
            <li class="nav-item"><a class="nav-link <?php echo e(Request::is('currencies*') ? 'active' : ''); ?>" href="<?php echo e(route('currencies.index')); ?>"><i class="fas fa-money-bill-wave"></i> <?php echo e(__('main.currencies')); ?></a></li>
            <li class="nav-item"><a class="nav-link <?php echo e(Request::is('settings*') ? 'active' : ''); ?>" href="<?php echo e(route('settings.index')); ?>"><i class="fas fa-cog"></i> <?php echo e(__('main.settings')); ?></a></li>
        </ul>
        <div class="sidebar-footer">
            <div class="nav-item dropdown">
                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                    <i class="fas fa-user-circle"></i> <?php echo e(Auth::user()->name); ?>

                </a>
                <div class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="<?php echo e(route('logout')); ?>"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i> <?php echo e(__('main.logout')); ?>

                    </a>
                    <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" class="d-none">
                        <?php echo csrf_field(); ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?> <?php /**PATH C:\Users\mohja\Desktop\projects\fintks-accounting\resources\views/layouts/sidebar.blade.php ENDPATH**/ ?>