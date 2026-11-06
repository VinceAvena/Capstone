<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['logs', 'admin' => false]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['logs', 'admin' => false]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $who = function ($log) use ($admin) {
        if (! $log->user) {
            return 'TindaPresyo system';
        }
        $role = match (true) {
            $log->user->isAdmin() => 'Market administration',
            $log->user->isVendor() => 'Vendor',
            $log->user->isShopper() => 'Shopper',
            default => 'Market inspector',
        };

        return $admin ? $log->user->name . ' (' . $role . ')' : $role;
    };
?>
<ul class="timeline">
    <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <li class="<?php echo e($log->type); ?>">
            <div>
                <?php if($log->type === 'status'): ?>
                    <strong>
                        <?php if($log->from_status): ?>
                            <?php echo e(\App\Models\Complaint::STATUSES[$log->from_status] ?? $log->from_status); ?> →
                        <?php endif; ?>
                        <?php echo e(\App\Models\Complaint::STATUSES[$log->to_status] ?? $log->to_status); ?>

                    </strong>
                <?php elseif($log->type === 'message'): ?>
                    <strong>Message</strong>
                <?php else: ?>
                    <strong>Notice</strong>
                <?php endif; ?>
                <span class="when">· <?php echo e($who($log)); ?> · <?php echo e($log->created_at->format('M j, Y g:i A')); ?></span>
            </div>
            <?php if($log->message): ?>
                <div class="bubble"><?php echo e($log->message); ?></div>
            <?php endif; ?>
        </li>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <li><span class="muted">No activity yet.</span></li>
    <?php endif; ?>
</ul>
<?php /**PATH C:\Users\User\Documents\tindapresyo\resources\views/components/timeline.blade.php ENDPATH**/ ?>