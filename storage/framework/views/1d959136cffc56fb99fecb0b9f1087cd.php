<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'TindaPresyo'); ?> · <?php echo e(config('tindapresyo.market_name')); ?></title>
    <link rel="stylesheet" href="<?php echo e(asset('css/app.css')); ?>">
    <script src="<?php echo e(asset('js/app.js')); ?>" defer></script>
</head>
<body>


<?php $u = auth()->user(); ?>
<header class="topbar">
    <div class="container">
        <a class="brand" href="<?php echo e(route('home')); ?>" aria-label="TindaPresyo home">
            <svg viewBox="0 0 32 32" fill="none" aria-hidden="true">
                <path d="M4 16.5V5a1 1 0 0 1 1-1h11.5a1 1 0 0 1 .7.3l10.5 10.5a1 1 0 0 1 0 1.4l-10.9 10.9a1 1 0 0 1-1.4 0L4.3 17.2a1 1 0 0 1-.3-.7Z" fill="#e9a21b"/>
                <circle cx="10" cy="10" r="2.2" fill="#114a30"/>
                <path d="M14 17.5h6M14 20.5h6M16 14.5h2.6a1.6 1.6 0 0 1 0 3.2H15.6" stroke="#114a30" stroke-width="1.6" stroke-linecap="round"/>
            </svg>
            <span>TindaPresyo<small><?php echo e(config('tindapresyo.market_name')); ?></small></span>
        </a>

        <button class="nav-toggle" id="nav-toggle" aria-label="Open menu" aria-expanded="false" aria-controls="main-nav">☰ Menu</button>

        <nav class="nav" id="main-nav" aria-label="Main menu">
            <?php if(auth()->guard()->guest()): ?>
                <a href="<?php echo e(route('prices.index')); ?>" class="<?php echo e(request()->routeIs('prices.*') ? 'active' : ''); ?>">Price Board</a>
                <a href="<?php echo e(route('track')); ?>" class="<?php echo e(request()->routeIs('track') ? 'active' : ''); ?>">Track a Complaint</a>
                <a href="<?php echo e(route('login')); ?>" class="<?php echo e(request()->routeIs('login') ? 'active' : ''); ?>">Log in</a>
                <a href="<?php echo e(route('register')); ?>" class="<?php echo e(request()->routeIs('register') ? 'active' : ''); ?>">Register</a>
            <?php else: ?>
                <a href="<?php echo e(route('dashboard')); ?>" class="<?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>">Dashboard</a>

                <?php if($u->isAdmin()): ?>
                    <a href="<?php echo e(route('admin.complaints.index')); ?>" class="<?php echo e(request()->routeIs('admin.complaints.*') ? 'active' : ''); ?>">Complaints</a>
                    <a href="<?php echo e(route('admin.stalls.index')); ?>" class="<?php echo e(request()->routeIs('admin.stalls.*') ? 'active' : ''); ?>">Stalls</a>
                    <a href="<?php echo e(route('admin.flagged.index')); ?>" class="<?php echo e(request()->routeIs('admin.flagged.*') ? 'active' : ''); ?>">Flagged</a>
                    <a href="<?php echo e(route('admin.commodities.index')); ?>" class="<?php echo e(request()->routeIs('admin.commodities.*') ? 'active' : ''); ?>">Commodities</a>
                    <a href="<?php echo e(route('admin.users.index')); ?>" class="<?php echo e(request()->routeIs('admin.users.*') ? 'active' : ''); ?>">Users</a>
                    <a href="<?php echo e(route('admin.reports.index')); ?>" class="<?php echo e(request()->routeIs('admin.reports.*') ? 'active' : ''); ?>">Reports</a>
                    <a href="<?php echo e(route('admin.evaluation.index')); ?>" class="<?php echo e(request()->routeIs('admin.evaluation.*') ? 'active' : ''); ?>">Evaluation</a>
                <?php endif; ?>

                <?php if($u->isVendor() || $u->isStaff()): ?>
                    <a href="<?php echo e(route('posting.index')); ?>" class="<?php echo e(request()->routeIs('posting.*') ? 'active' : ''); ?>">Post Prices</a>
                <?php endif; ?>
                <?php if($u->isStaff()): ?>
                    <a href="<?php echo e(route('verification.index')); ?>" class="<?php echo e(request()->routeIs('verification.*') ? 'active' : ''); ?>">Verify Prices</a>
                <?php endif; ?>
                <?php if($u->isVendor()): ?>
                    <a href="<?php echo e(route('vendor.stalls.index')); ?>" class="<?php echo e(request()->routeIs('vendor.stalls.*') ? 'active' : ''); ?>">My Stalls</a>
                    <a href="<?php echo e(route('vendor.complaints.index')); ?>" class="<?php echo e(request()->routeIs('vendor.complaints.*') ? 'active' : ''); ?>">Complaints</a>
                <?php endif; ?>
                <?php if($u->isShopper()): ?>
                    <a href="<?php echo e(route('shopper.complaints.create')); ?>" class="<?php echo e(request()->routeIs('shopper.complaints.create') ? 'active' : ''); ?>">Report Overpricing</a>
                    <a href="<?php echo e(route('shopper.complaints.index')); ?>" class="<?php echo e(request()->routeIs('shopper.complaints.index', 'shopper.complaints.show') ? 'active' : ''); ?>">My Complaints</a>
                <?php endif; ?>

                <a href="<?php echo e(route('prices.index')); ?>" class="<?php echo e(request()->routeIs('prices.*') ? 'active' : ''); ?>">Price Board</a>
                <?php if (! ($u->isAdmin())): ?>
                    <a href="<?php echo e(route('evaluation.form')); ?>" class="<?php echo e(request()->routeIs('evaluation.form') ? 'active' : ''); ?>">Evaluate</a>
                <?php endif; ?>

                <span class="who"><?php echo e($u->name); ?></span>
                <a href="<?php echo e(route('profile.edit')); ?>" class="<?php echo e(request()->routeIs('profile.*') ? 'active' : ''); ?>">Profile</a>
                <form method="POST" action="<?php echo e(route('logout')); ?>" class="inline">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="linklike">Log out</button>
                </form>
            <?php endif; ?>
        </nav>
    </div>
</header>


<main class="container">
    <?php if(session('success')): ?>
        <div class="alert success" role="status"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="alert error" role="alert"><?php echo e(session('error')); ?></div>
    <?php endif; ?>
    <?php if($errors->any()): ?>
        <div class="alert error" role="alert">
            <strong>Please fix the following:</strong>
            <ul>
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php echo $__env->yieldContent('content'); ?>
</main>

<footer class="footer">
    <div class="container">
        TindaPresyo: Web-Based Price Monitoring and Overpricing Complaint System ·
        St. Paul University Philippines · Capstone Project
    </div>
</footer>

</body>
</html>
<?php /**PATH C:\Users\User\Documents\tindapresyo\resources\views/layouts/app.blade.php ENDPATH**/ ?>