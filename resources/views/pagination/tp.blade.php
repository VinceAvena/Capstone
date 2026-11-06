{{-- Simple "Previous / Page x of y / Next" links used by every list. --}}
@if ($paginator->hasPages())
    <nav class="pagination" role="navigation" aria-label="Pagination">
        @if ($paginator->onFirstPage())
            <span class="btnlike disabled">← Previous</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev">← Previous</a>
        @endif

        <span>Page {{ $paginator->currentPage() }}@if (method_exists($paginator, 'lastPage')) of {{ $paginator->lastPage() }}@endif</span>

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next">Next →</a>
        @else
            <span class="btnlike disabled">Next →</span>
        @endif
    </nav>
@endif
