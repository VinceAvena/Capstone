<?php $__env->startSection('title', 'Review ' . $complaint->reference_no); ?>

<?php $__env->startSection('content'); ?>
<?php
    $buttonLabels = [
        'under_review' => 'Start review',
        'confirmed' => 'Confirm overpricing',
        'dismissed' => 'Dismiss complaint',
        'resolved' => 'Mark as resolved',
    ];
?>
<div class="page-head">
    <div><h1><?php echo e($complaint->reference_no); ?></h1><p>Filed <?php echo e($complaint->created_at->format('M j, Y g:i A')); ?> by <?php echo e($complaint->shopper->name); ?></p></div>
    <div class="actions"><?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
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
<?php endif; ?><a class="btn ghost" href="<?php echo e(route('admin.complaints.index')); ?>">← All complaints</a></div>
</div>

<div class="grid split">
    <div>
        
        <div class="card mb">
            <h2>Verification: posted price vs. reported price</h2>
            <p><?php echo e($complaint->commodity->name); ?> at stall <a href="<?php echo e(route('stalls.show', $complaint->stall)); ?>"><?php echo e($complaint->stall->stall_number); ?> · <?php echo e($complaint->stall->name); ?></a>
               (vendor: <?php echo e($complaint->stall->vendor->name); ?>)</p>
            <div class="compare">
                <div class="box"><div class="muted small">Posted price</div><div class="big">₱<?php echo e(number_format($complaint->posted_price, 2)); ?></div><div class="small muted">posted <?php echo e(\Illuminate\Support\Carbon::parse($complaint->posted_price_date)->format('M j, Y')); ?></div></div>
                <div class="box over"><div class="muted small">Reported price</div><div class="big">₱<?php echo e(number_format($complaint->reported_price, 2)); ?></div></div>
                <div class="box"><div class="muted small">Overcharge</div><div class="big">+₱<?php echo e(number_format($complaint->overcharge(), 2)); ?></div><div class="small muted"><?php echo e($complaint->overchargePercent()); ?>% over</div></div>
            </div>
            <?php if($currentPrice && (float) $currentPrice->price !== (float) $complaint->posted_price): ?>
                <div class="alert warn small mt">The stall's <strong>current</strong> posted price is now ₱<?php echo e(number_format($currentPrice->price, 2)); ?> (posted <?php echo e(\Illuminate\Support\Carbon::parse($currentPrice->price_date)->format('M j')); ?>). The comparison above uses the price that was posted when the complaint was filed.</div>
            <?php endif; ?>
            <?php if($complaint->description): ?><p class="mt"><strong>Shopper's note:</strong> <?php echo e($complaint->description); ?></p><?php endif; ?>
            <div class="evidence mt">
                <strong>Photo evidence</strong><br>
                <a href="<?php echo e(route('complaints.photo', $complaint)); ?>" target="_blank" rel="noopener">
                    <img src="<?php echo e(route('complaints.photo', $complaint)); ?>" alt="Photo evidence for <?php echo e($complaint->reference_no); ?>">
                </a>
            </div>
        </div>

        
        <div class="card mb">
            <h2>Update status</h2>
            <?php if($complaint->allowedTransitions()): ?>
                <form method="POST" action="<?php echo e(route('admin.complaints.status', $complaint)); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="field">
                        <label for="note">Remarks / reason / resolution <span class="muted">(required to dismiss or resolve)</span></label>
                        <textarea id="note" name="note" maxlength="1000" class="<?php $__errorArgs = ['note'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"><?php echo e(old('note')); ?></textarea>
                        <div class="hint">The shopper can read this note.</div>
                    </div>
                    <div class="actions">
                        <?php $__currentLoopData = $complaint->allowedTransitions(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $next): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <button type="submit" name="status" value="<?php echo e($next); ?>"
                                    class="btn <?php echo e($next === 'dismissed' ? 'ghost' : ($next === 'confirmed' ? 'danger' : 'primary')); ?>">
                                <?php echo e($buttonLabels[$next] ?? $next); ?>

                            </button>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </form>
            <?php else: ?>
                <p class="muted">This complaint is <strong><?php echo e($complaint->statusLabel()); ?></strong>, so no further status changes are possible.</p>
            <?php endif; ?>
        </div>

        <?php if($complaint->canExchangeMessages()): ?>
            <div class="card">
                <h2>Message the vendor</h2>
                <form method="POST" action="<?php echo e(route('complaints.messages.store', $complaint)); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="field">
                        <textarea name="message" required maxlength="1000" placeholder="Ask the vendor to explain, correct the price, or confirm the action taken." class="<?php $__errorArgs = ['message'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"><?php echo e(old('message')); ?></textarea>
                        <div class="hint">Only you and the vendor can see messages. The shopper cannot.</div>
                    </div>
                    <button type="submit" class="btn primary">Send to vendor</button>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <div>
        <div class="card mb">
            <h2>Stall record</h2>
            <dl class="facts">
                <dt>Confirmed complaints</dt><dd><?php echo e($violationCount); ?> / <?php echo e(config('tindapresyo.violation_threshold')); ?> to flag</dd>
                <dt>Flag</dt><dd><?php if($complaint->stall->is_flagged): ?><span class="badge flag">Flagged repeat violator</span><?php else: ?> Not flagged <?php endif; ?></dd>
                <dt>Vendor phone</dt><dd><?php echo e($complaint->stall->vendor->phone ?: '-'); ?></dd>
            </dl>
            <?php if($stallHistory->isNotEmpty()): ?>
                <h3 class="mt">Other complaints at this stall</h3>
                <ul class="small" style="padding-left:18px;margin:0">
                    <?php $__currentLoopData = $stallHistory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><a href="<?php echo e(route('admin.complaints.show', $h)); ?>"><?php echo e($h->reference_no); ?></a> · <?php echo e($h->commodity->name); ?> · <?php echo e(\App\Models\Complaint::STATUSES[$h->status]); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            <?php endif; ?>
        </div>

        <div class="card">
            <h2>History</h2>
            <?php if (isset($component)) { $__componentOriginal93f2afea2d7941ca7799292711b7f46f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal93f2afea2d7941ca7799292711b7f46f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.timeline','data' => ['logs' => $logs,'admin' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('timeline'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['logs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($logs),'admin' => true]); ?>
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
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\User\Documents\tindapresyo\resources\views/admin/complaints/show.blade.php ENDPATH**/ ?>