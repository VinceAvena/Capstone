<?php $__env->startSection('title', 'Administrator Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-head">
    <div><h1>Market Administrator Dashboard</h1><p>Welcome back, <?php echo e(auth()->user()->name); ?>. Here is what needs attention today.</p></div>
    <a class="btn primary" href="<?php echo e(route('admin.reports.index')); ?>">Generate reports</a>
</div>

<h2>Complaints</h2>
<div class="grid cols-4 mb">
    <a class="card stat <?php echo e($counts['pending'] ? 'warn' : ''); ?>" href="<?php echo e(route('admin.complaints.index', ['status' => 'pending'])); ?>"><div class="num"><?php echo e($counts['pending']); ?></div><div class="lbl">Pending: waiting for review</div></a>
    <a class="card stat" href="<?php echo e(route('admin.complaints.index', ['status' => 'under_review'])); ?>"><div class="num"><?php echo e($counts['under_review']); ?></div><div class="lbl">Under review</div></a>
    <a class="card stat <?php echo e($counts['confirmed'] ? 'bad' : ''); ?>" href="<?php echo e(route('admin.complaints.index', ['status' => 'confirmed'])); ?>"><div class="num"><?php echo e($counts['confirmed']); ?></div><div class="lbl">Confirmed: not yet resolved</div></a>
    <a class="card stat" href="<?php echo e(route('admin.complaints.index', ['status' => 'resolved'])); ?>"><div class="num"><?php echo e($counts['resolved_month']); ?></div><div class="lbl">Resolved this month</div></a>
</div>

<h2>Stalls and prices</h2>
<div class="grid cols-4 mb">
    <a class="card stat <?php echo e($counts['flagged'] ? 'bad' : ''); ?>" href="<?php echo e(route('admin.flagged.index')); ?>"><div class="num"><?php echo e($counts['flagged']); ?></div><div class="lbl">Flagged repeat violators</div></a>
    <a class="card stat <?php echo e($counts['stalls_pending'] ? 'warn' : ''); ?>" href="<?php echo e(route('admin.stalls.index', ['status' => 'pending'])); ?>"><div class="num"><?php echo e($counts['stalls_pending']); ?></div><div class="lbl">Stalls waiting for approval</div></a>
    <a class="card stat <?php echo e($counts['not_posted'] ? 'warn' : ''); ?>" href="<?php echo e(route('verification.index')); ?>"><div class="num"><?php echo e($counts['not_posted']); ?></div><div class="lbl">Stalls with no price today</div></a>
    <a class="card stat" href="<?php echo e(route('verification.index')); ?>"><div class="num"><?php echo e($counts['unverified']); ?></div><div class="lbl">Today's prices not yet verified</div></a>
</div>

<div class="card flush">
    <div style="padding:16px 18px 0"><h2>Latest complaints</h2></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Reference</th><th>Stall</th><th>Commodity</th><th class="num">Posted → Reported</th><th>Status</th><th></th></tr></thead>
            <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $recent; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td class="nowrap"><?php echo e($c->reference_no); ?></td>
                    <td><?php echo e($c->stall->stall_number); ?> · <?php echo e($c->stall->name); ?></td>
                    <td><?php echo e($c->commodity->name); ?></td>
                    <td class="num">₱<?php echo e(number_format($c->posted_price, 2)); ?> → <span class="up">₱<?php echo e(number_format($c->reported_price, 2)); ?></span></td>
                    <td><?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.status-badge','data' => ['status' => $c->status]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($c->status)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $attributes = $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $component = $__componentOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?></td>
                    <td class="right"><a class="btn small ghost" href="<?php echo e(route('admin.complaints.show', $c)); ?>">Open</a></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="6" class="empty">No complaints yet.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\User\Documents\tindapresyo\resources\views/dashboard/admin.blade.php ENDPATH**/ ?>