<?php $__env->startSection('title', 'Track a Complaint'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-head">
    <div><h1>Track a complaint</h1><p>Enter the reference number you received when you filed your complaint.</p></div>
</div>

<form method="GET" action="<?php echo e(route('track')); ?>" class="filters">
    <div class="field" style="max-width:340px">
        <label for="ref">Reference number</label>
        <input type="text" id="ref" name="ref" value="<?php echo e($ref); ?>" placeholder="TP-20261102-ABCDE" required>
    </div>
    <button type="submit" class="btn primary">Track</button>
</form>

<?php if($ref !== '' && ! $complaint): ?>
    <div class="alert error">No complaint was found with the reference number <strong><?php echo e($ref); ?></strong>. Please check it and try again.</div>
<?php endif; ?>

<?php if($complaint): ?>
    <div class="grid split">
        <div class="card">
            <h2>Status history</h2>
            <?php if (isset($component)) { $__componentOriginal93f2afea2d7941ca7799292711b7f46f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal93f2afea2d7941ca7799292711b7f46f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.timeline','data' => ['logs' => $logs]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('timeline'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['logs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($logs)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal93f2afea2d7941ca7799292711b7f46f)): ?>
<?php $attributes = $__attributesOriginal93f2afea2d7941ca7799292711b7f46f; ?>
<?php unset($__attributesOriginal93f2afea2d7941ca7799292711b7f46f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal93f2afea2d7941ca7799292711b7f46f)): ?>
<?php $component = $__componentOriginal93f2afea2d7941ca7799292711b7f46f; ?>
<?php unset($__componentOriginal93f2afea2d7941ca7799292711b7f46f); ?>
<?php endif; ?>
        </div>
        <div class="card">
            <h3><?php echo e($complaint->reference_no); ?></h3>
            <p><?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.status-badge','data' => ['status' => $complaint->status]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($complaint->status)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $attributes = $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $component = $__componentOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?></p>
            <dl class="facts">
                <dt>Stall</dt><dd><?php echo e($complaint->stall->stall_number); ?> · <?php echo e($complaint->stall->name); ?></dd>
                <dt>Commodity</dt><dd><?php echo e($complaint->commodity->name); ?></dd>
                <dt>Filed</dt><dd><?php echo e($complaint->created_at->format('M j, Y')); ?></dd>
            </dl>
        </div>
    </div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Documents\college\Fourth Year (irregular student era)\1st sem\Capstone 1\tindapresyo\resources\views/track/index.blade.php ENDPATH**/ ?>