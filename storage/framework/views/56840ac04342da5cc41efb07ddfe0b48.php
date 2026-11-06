<?php $__env->startSection('title', 'Home'); ?>

<?php $__env->startSection('content'); ?>
<section class="hero">
    <h1>Know the posted price. Report overpricing. Keep the market fair.</h1>
    <p>TindaPresyo lets vendors post daily prices, shoppers report when they are charged more than the posted price, and the market administration office verify and follow up every complaint.</p>
    <div class="actions">
        <a class="btn accent" href="<?php echo e(route('prices.index')); ?>">View today's prices</a>
        <?php if(auth()->guard()->guest()): ?>
            <a class="btn ghost" href="<?php echo e(route('register')); ?>">Create an account</a>
        <?php else: ?>
            <a class="btn ghost" href="<?php echo e(route('dashboard')); ?>">Go to my dashboard</a>
        <?php endif; ?>
    </div>
</section>

<div class="grid cols-4 mb">
    <div class="card stat"><div class="num"><?php echo e($stats['stalls']); ?></div><div class="lbl">Registered stalls</div></div>
    <div class="card stat"><div class="num"><?php echo e($stats['commodities']); ?></div><div class="lbl">Monitored commodities</div></div>
    <div class="card stat"><div class="num"><?php echo e($stats['postedToday']); ?></div><div class="lbl">Prices posted today</div></div>
    <div class="card stat"><div class="num"><?php echo e($stats['resolved']); ?></div><div class="lbl">Complaints resolved</div></div>
</div>

<div class="grid split">
    <div class="card flush">
        <div style="padding:16px 18px 0"><h2>Latest posted prices</h2></div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Commodity</th><th>Stall</th><th class="num">Price</th><th>Posted</th></tr></thead>
                <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $latest; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($p->commodity->name); ?></td>
                        <td><a href="<?php echo e(route('stalls.show', $p->stall)); ?>"><?php echo e($p->stall->stall_number); ?></a> · <?php echo e($p->stall->name); ?></td>
                        <td class="num money">₱<?php echo e(number_format($p->price, 2)); ?> <span class="muted small">/ <?php echo e($p->commodity->unit); ?></span></td>
                        <td class="nowrap"><?php echo e(\Illuminate\Support\Carbon::parse($p->price_date)->format('M j')); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="4" class="empty">No prices have been posted yet.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div style="padding:12px 18px"><a href="<?php echo e(route('prices.index')); ?>">See the full price board →</a></div>
    </div>

    <div class="card">
        <h2>How it works</h2>
        <ol class="steps">
            <li><strong>Vendors post prices.</strong> Each stall updates its prices every day. Inspectors verify them during rounds.</li>
            <li><strong>Shoppers compare.</strong> Anyone can check the price board before buying.</li>
            <li><strong>Overcharged? Report it.</strong> Attach a photo of the receipt or price tag.</li>
            <li><strong>The office verifies.</strong> The administrator compares your report with the posted price and follows it until it is resolved.</li>
        </ol>
        <a class="btn ghost block" href="<?php echo e(route('track')); ?>">Track a complaint</a>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\User\Documents\tindapresyo\resources\views/home.blade.php ENDPATH**/ ?>