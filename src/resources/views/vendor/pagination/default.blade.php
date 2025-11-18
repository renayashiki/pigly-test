@if ($paginator->hasPages())
    <nav class="pagination-links" role="navigation" aria-label="Pagination Navigation">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="page-link disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">&lt;</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="page-link" rel="prev" aria-label="@lang('pagination.previous')">&lt;</a>
        @endif

        {{-- Pagination Elements --}}
        {{-- 修正2: 現在のページから最大3ページ分のみを表示するロジックに変更 --}}
        @php
            $current = $paginator->currentPage();
            $last = $paginator->lastPage();
            // 現在のページから最大3ページ (例: 1, 2, 3 または 4, 5, 6) を計算
            $block_start = max(1, $current - (($current - 1) % 3)); // ブロックの開始ページ (1, 4, 7...)
            $block_end = min($block_start + 2, $last); // ブロックの終了ページ (3, 6, 9...)
        @endphp

        @foreach (range($block_start, $block_end) as $page)
            @if ($page == $paginator->currentPage())
                <span class="page-link active" aria-current="page">{{ $page }}</span>
            @else
                <a href="{{ $paginator->url($page) }}" class="page-link">{{ $page }}</a>
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="page-link" rel="next" aria-label="@lang('pagination.next')">&gt;</a>
        @else
            <span class="page-link disabled" aria-disabled="true" aria-label="@lang('pagination.next')">&gt;</span>
        @endif
    </nav>
@endif