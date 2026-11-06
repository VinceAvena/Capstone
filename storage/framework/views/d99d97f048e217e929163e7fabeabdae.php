
<?php if($paginator->hasPages()): ?>
    <nav class="pagination" role="navigation" aria-label="Pagination">
        <?php if($paginator->onFirstPage()): ?>
            <span class="btnlike disabled">← Previous</span>
        <?php else: ?>
            <a href="<?php echo e($paginator->previousPageUrl()); ?>" rel="prev">← Previous</a>
        <?php endif; ?>

        <span>Page <?php echo e($paginator->currentPage()); ?><?php if(method_exists($paginator, 'lastPage')): ?> of <?php echo e($paginator->lastPage()); ?><?php endif; ?></span>

        <?php if($paginator->hasMorePages()): ?>
            <a href="<?php echo e($paginator->nextPageUrl()); ?>" rel="next">Next →</a>
        <?php else: ?>
            <span class="btnlike disabled">Next →</span>
        <?php endif; ?>
    </nav>
<?php endif; ?>
<?php /**PATH E:\Documents\college\Fourth Year (irregular student era)\1st sem\Capstone 1\tindapresyo\resources\views/pagination/tp.blade.php ENDPATH**/ ?>