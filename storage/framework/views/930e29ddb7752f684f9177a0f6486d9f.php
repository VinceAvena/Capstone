<?php $__env->startSection('title', 'Reports'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-head">
    <div><h1>Price monitoring and compliance reports</h1><p>Choose a period, then print this page or download each report as a spreadsheet (CSV).</p></div>
    <button type="button" class="btn primary no-print" data-print>🖨 Print report</button>
</div>

<form method="GET" action="<?php echo e(route('admin.reports.index')); ?>" class="filters">
    <div class="field"><label for="from">From</label><input type="date" id="from" name="from" value="<?php echo e($from); ?>"></div>
    <div class="field"><label for="to">To</label><input type="date" id="to" name="to" value="<?php echo e($to); ?>"></div>
    <div class="field">
        <label for="section">Section</label>
        <select id="section" name="section">
            <option value="">All sections</option>
            <?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($s); ?>" <?php if($section === $s): echo 'selected'; endif; ?>><?php echo e($s); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
    <div class="actions"><button class="btn primary" type="submit">Generate</button></div>
</form>

<div class="print-only mb">
    <h1><?php echo e($marketName); ?>: Price Monitoring and Compliance Report</h1>
    <p>Period: <?php echo e(\Illuminate\Support\Carbon::parse($from)->format('M j, Y')); ?> to <?php echo e(\Illuminate\Support\Carbon::parse($to)->format('M j, Y')); ?>

       <?php if($section): ?> · Section: <?php echo e($section); ?> <?php endif; ?>
       · Generated <?php echo e(now()->format('M j, Y g:i A')); ?></p>
</div>


<div class="card flush mb">
    <div class="actions" style="padding:16px 18px 0;justify-content:space-between">
        <h2>1. Price monitoring summary</h2>
        <a class="btn small ghost no-print" href="<?php echo e(route('admin.reports.export', array_merge(['type' => 'prices'], request()->only(['from', 'to', 'section'])))); ?>">Download CSV</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Commodity</th><th>Category</th><th class="num">Stalls posting</th><th class="num">Entries</th><th class="num">Lowest</th><th class="num">Average</th><th class="num">Highest</th></tr></thead>
            <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $priceSummary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><strong><?php echo e($r->name); ?></strong> <span class="muted small">/ <?php echo e($r->unit); ?></span></td>
                    <td><?php echo e($r->category); ?></td>
                    <td class="num"><?php echo e($r->stalls); ?></td>
                    <td class="num"><?php echo e($r->entries); ?></td>
                    <td class="num">₱<?php echo e(number_format($r->min_price, 2)); ?></td>
                    <td class="num">₱<?php echo e(number_format($r->avg_price, 2)); ?></td>
                    <td class="num">₱<?php echo e(number_format($r->max_price, 2)); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="7" class="empty">No prices were posted in this period.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>


<div class="card flush mb">
    <div class="actions" style="padding:16px 18px 0;justify-content:space-between">
        <h2>2. Price posting compliance per stall</h2>
        <a class="btn small ghost no-print" href="<?php echo e(route('admin.reports.export', array_merge(['type' => 'compliance'], request()->only(['from', 'to', 'section'])))); ?>">Download CSV</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Stall</th><th>Vendor</th><th>Section</th><th class="num">Days posted</th><th class="num">Compliance</th><th>Last posted</th></tr></thead>
            <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $compliance; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><strong><?php echo e($s->stall_number); ?></strong> · <?php echo e($s->name); ?></td>
                    <td><?php echo e($s->vendor?->name); ?></td>
                    <td><?php echo e($s->section); ?></td>
                    <td class="num"><?php echo e((int) $s->days_posted); ?> of <?php echo e($s->days_total); ?></td>
                    <td class="num"><span class="badge <?php echo e($s->rate >= 80 ? 'ok' : 'stale'); ?>"><?php echo e($s->rate); ?>%</span></td>
                    <td><?php echo e($s->last_posted ? \Illuminate\Support\Carbon::parse($s->last_posted)->format('M j, Y') : 'Never'); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="6" class="empty">No approved stalls.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>


<div class="card mb">
    <div class="actions" style="justify-content:space-between">
        <h2>3. Overpricing complaints in the period</h2>
        <a class="btn small ghost no-print" href="<?php echo e(route('admin.reports.export', array_merge(['type' => 'complaints'], request()->only(['from', 'to', 'section'])))); ?>">Download CSV</a>
    </div>
    <div class="grid cols-4 mb">
        <div class="card stat"><div class="num"><?php echo e($complaintTotal); ?></div><div class="lbl">Complaints filed</div></div>
        <?php $__currentLoopData = ['pending', 'under_review', 'confirmed', 'dismissed', 'resolved']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="card stat"><div class="num"><?php echo e($complaintsByStatus[$st] ?? 0); ?></div><div class="lbl"><?php echo e(\App\Models\Complaint::STATUSES[$st]); ?></div></div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <div class="card stat"><div class="num"><?php echo e($avgOvercharge !== null ? $avgOvercharge . '%' : '-'); ?></div><div class="lbl">Average overcharge (confirmed)</div></div>
    </div>

    <h3>Confirmed violations per stall</h3>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Stall</th><th class="num">Confirmed complaints</th><th class="num">Average overcharge</th><th>Flag</th></tr></thead>
            <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $violations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><strong><?php echo e($v->stall->stall_number); ?></strong> · <?php echo e($v->stall->name); ?></td>
                    <td class="num"><?php echo e($v->count); ?></td>
                    <td class="num"><?php echo e($v->avg_percent); ?>%</td>
                    <td><?php if($v->stall->is_flagged): ?><span class="badge flag">Flagged</span><?php else: ?> - <?php endif; ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="4" class="empty">No confirmed violations in this period.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>


<div class="card flush">
    <div class="actions" style="padding:16px 18px 0;justify-content:space-between">
        <h2>4. Flagged repeat violators (current)</h2>
        <a class="btn small ghost no-print" href="<?php echo e(route('admin.reports.export', array_merge(['type' => 'flagged'], request()->only(['from', 'to', 'section'])))); ?>">Download CSV</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Stall</th><th>Vendor</th><th>Section</th><th>Flagged on</th><th class="num">Confirmed</th></tr></thead>
            <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $flagged; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><strong><?php echo e($s->stall_number); ?></strong> · <?php echo e($s->name); ?></td>
                    <td><?php echo e($s->vendor?->name); ?></td>
                    <td><?php echo e($s->section); ?></td>
                    <td><?php echo e($s->flagged_at?->format('M j, Y')); ?></td>
                    <td class="num"><?php echo e($s->violationCount()); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="5" class="empty">No stalls are flagged.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\User\Documents\tindapresyo\resources\views/admin/reports/index.blade.php ENDPATH**/ ?>