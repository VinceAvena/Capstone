<?php $__env->startSection('title', 'Complaints'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-head"><div><h1>Overpricing complaints</h1><p>Verify each complaint against the posted price and follow it until it is resolved.</p></div></div>

<div class="tabs">
    <a href="<?php echo e(route('admin.complaints.index')); ?>" class="<?php echo e(empty($filters['status']) ? 'active' : ''); ?>">All (<?php echo e($counts->sum()); ?>)</a>
    <?php $__currentLoopData = \App\Models\Complaint::STATUSES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e(route('admin.complaints.index', ['status' => $key])); ?>" class="<?php echo e(($filters['status'] ?? '') === $key ? 'active' : ''); ?>"><?php echo e($label); ?> (<?php echo e($counts[$key] ?? 0); ?>)</a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>

<form method="GET" action="<?php echo e(route('admin.complaints.index')); ?>" class="filters">
    <?php if(! empty($filters['status'])): ?> <input type="hidden" name="status" value="<?php echo e($filters['status']); ?>"> <?php endif; ?>
    <div class="field"><label for="q">Search</label><input type="search" id="q" name="q" value="<?php echo e($filters['q'] ?? ''); ?>" placeholder="Reference, stall or shopper"></div>
    <div class="field"><label for="from">From</label><input type="date" id="from" name="from" value="<?php echo e($filters['from'] ?? ''); ?>"></div>
    <div class="field"><label for="to">To</label><input type="date" id="to" name="to" value="<?php echo e($filters['to'] ?? ''); ?>"></div>
    <div class="actions"><button class="btn primary" type="submit">Filter</button><a class="btn ghost" href="<?php echo e(route('admin.complaints.index')); ?>">Reset</a></div>
</form>

<div class="card flush">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Reference</th><th>Filed</th><th>Stall</th><th>Commodity</th><th class="num">Posted → Reported</th><th>Shopper</th><th>Status</th><th></th></tr></thead>
            <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $complaints; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td class="nowrap"><?php echo e($c->reference_no); ?></td>
                    <td class="nowrap"><?php echo e($c->created_at->format('M j, Y')); ?></td>
                    <td><?php echo e($c->stall->stall_number); ?> · <?php echo e($c->stall->name); ?> <?php if($c->stall->is_flagged): ?><span class="badge flag">Flagged</span><?php endif; ?></td>
                    <td><?php echo e($c->commodity->name); ?></td>
                    <td class="num">₱<?php echo e(number_format($c->posted_price, 2)); ?> → <span class="up">₱<?php echo e(number_format($c->reported_price, 2)); ?></span></td>
                    <td><?php echo e($c->shopper->name); ?></td>
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
                    <td class="right"><a class="btn small primary" href="<?php echo e(route('admin.complaints.show', $c)); ?>">Review</a></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="8" class="empty">No complaints match your filters.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php echo e($complaints->links()); ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\User\Documents\tindapresyo\resources\views/admin/complaints/index.blade.php ENDPATH**/ ?>