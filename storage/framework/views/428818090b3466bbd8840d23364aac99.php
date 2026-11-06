<?php $__env->startSection('title', 'Price Board'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-head">
    <div>
        <h1>Price Board</h1>
        <p>Current posted prices of every approved stall. Compare before you buy.</p>
    </div>
    <?php if(auth()->guard()->check()): ?>
        <?php if(auth()->user()->isShopper()): ?>
            <a class="btn accent" href="<?php echo e(route('shopper.complaints.create')); ?>">Report overpricing</a>
        <?php endif; ?>
    <?php endif; ?>
</div>

<form method="GET" action="<?php echo e(route('prices.index')); ?>" class="filters">
    <div class="field">
        <label for="q">Search</label>
        <input type="search" id="q" name="q" value="<?php echo e($filters['q'] ?? ''); ?>" placeholder="Commodity, stall name or number">
    </div>
    <div class="field">
        <label for="commodity">Commodity</label>
        <select id="commodity" name="commodity">
            <option value="">All commodities</option>
            <?php $__currentLoopData = $commodities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($c->id); ?>" <?php if(($filters['commodity'] ?? '') == $c->id): echo 'selected'; endif; ?>><?php echo e($c->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
    <div class="field">
        <label for="section">Section</label>
        <select id="section" name="section">
            <option value="">All sections</option>
            <?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($s); ?>" <?php if(($filters['section'] ?? '') === $s): echo 'selected'; endif; ?>><?php echo e($s); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
    <div class="field">
        <label for="sort">Sort by</label>
        <select id="sort" name="sort">
            <option value="" <?php if(empty($filters['sort'])): echo 'selected'; endif; ?>>Commodity, cheapest first</option>
            <option value="price_asc" <?php if(($filters['sort'] ?? '') === 'price_asc'): echo 'selected'; endif; ?>>Lowest price</option>
            <option value="price_desc" <?php if(($filters['sort'] ?? '') === 'price_desc'): echo 'selected'; endif; ?>>Highest price</option>
            <option value="recent" <?php if(($filters['sort'] ?? '') === 'recent'): echo 'selected'; endif; ?>>Recently updated</option>
        </select>
    </div>
    <div class="actions">
        <button type="submit" class="btn primary">Apply</button>
        <a class="btn ghost" href="<?php echo e(route('prices.index')); ?>">Reset</a>
    </div>
</form>

<?php if($summary): ?>
    <div class="grid cols-4 mb">
        <div class="card stat"><div class="num">₱<?php echo e(number_format($summary['min'], 2)); ?></div><div class="lbl">Lowest</div></div>
        <div class="card stat"><div class="num">₱<?php echo e(number_format($summary['avg'], 2)); ?></div><div class="lbl">Average</div></div>
        <div class="card stat"><div class="num">₱<?php echo e(number_format($summary['max'], 2)); ?></div><div class="lbl">Highest</div></div>
        <div class="card stat"><div class="num"><?php echo e($summary['count']); ?></div><div class="lbl">Stalls selling it</div></div>
    </div>
<?php endif; ?>

<div class="card flush">
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>Commodity</th><th>Stall</th><th>Section</th><th class="num">Posted price</th><th>Last posted</th><th>Status</th></tr>
            </thead>
            <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $prices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><strong><?php echo e($p->commodity->name); ?></strong></td>
                    <td><a href="<?php echo e(route('stalls.show', $p->stall)); ?>"><?php echo e($p->stall->stall_number); ?></a> · <?php echo e($p->stall->name); ?></td>
                    <td><?php echo e($p->stall->section); ?></td>
                    <td class="num money">₱<?php echo e(number_format($p->price, 2)); ?> <span class="muted small">/ <?php echo e($p->commodity->unit); ?></span></td>
                    <td class="nowrap"><?php echo e(\Illuminate\Support\Carbon::parse($p->price_date)->format('M j, Y')); ?></td>
                    <td>
                        <?php if($summary && (float) $p->price === (float) $summary['min']): ?> <span class="badge s-under_review">Lowest price</span> <?php endif; ?>
                        <?php if($p->isStale()): ?> <span class="badge stale" title="Not updated today">Not updated today</span> <?php endif; ?>
                        <?php if($p->isVerified()): ?> <span class="badge ok" title="Checked by a market inspector">Verified</span> <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="6" class="empty">No prices match your search.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php echo e($prices->links()); ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Documents\college\Fourth Year (irregular student era)\1st sem\Capstone 1\tindapresyo\resources\views/prices/index.blade.php ENDPATH**/ ?>