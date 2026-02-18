@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="pagination-nav">
        <ul class="pagination">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="pagination-item pagination-item-disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                    <span class="pagination-link" aria-hidden="true">
                        <i class="fas fa-chevron-left"></i>
                    </span>
                </li>
            @else
                <li class="pagination-item">
                    <a href="{{ $paginator->previousPageUrl() }}" class="pagination-link" rel="prev" aria-label="@lang('pagination.previous')">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="pagination-item pagination-item-disabled" aria-disabled="true">
                        <span class="pagination-link">{{ $element }}</span>
                    </li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="pagination-item pagination-item-active" aria-current="page">
                                <span class="pagination-link pagination-link-active">{{ $page }}</span>
                            </li>
                        @else
                            <li class="pagination-item">
                                <a href="{{ $url }}" class="pagination-link">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="pagination-item">
                    <a href="{{ $paginator->nextPageUrl() }}" class="pagination-link" rel="next" aria-label="@lang('pagination.next')">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
            @else
                <li class="pagination-item pagination-item-disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                    <span class="pagination-link" aria-hidden="true">
                        <i class="fas fa-chevron-right"></i>
                    </span>
                </li>
            @endif
        </ul>
    </nav>
@endif

<style>
.pagination-nav {
    display: flex;
    justify-content: center;
    align-items: center;
}

.pagination {
    display: flex;
    list-style: none;
    padding: 0;
    margin: 0;
    gap: 0.5rem;
    align-items: center;
}

.pagination-item {
    margin: 0;
}

.pagination-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 40px;
    height: 40px;
    padding: 0.5rem 0.75rem;
    font-size: 0.9375rem;
    font-weight: 500;
    color: #64748B;
    background: #FFF;
    border: 1px solid #E2E8F0;
    border-radius: 10px;
    text-decoration: none;
    transition: all 0.2s ease;
    cursor: pointer;
}

.pagination-link:hover:not(.pagination-link-active) {
    background: #F8FAFC;
    border-color: #C1EC4A;
    color: #1A202C;
    transform: translateY(-1px);
}

.pagination-link-active {
    background: linear-gradient(135deg, #1A202C 0%, #2D3748 100%);
    border-color: #1A202C;
    color: #C1EC4A;
    font-weight: 600;
    box-shadow: 0 2px 8px rgba(26, 32, 44, 0.15);
}

.pagination-item-disabled .pagination-link {
    background: #F8FAFC;
    border-color: #E2E8F0;
    color: #94A3B8;
    cursor: not-allowed;
    opacity: 0.6;
}

.pagination-item-disabled .pagination-link:hover {
    transform: none;
    background: #F8FAFC;
    border-color: #E2E8F0;
}

.pagination-link i {
    font-size: 0.875rem;
}

@media (max-width: 576px) {
    .pagination {
        gap: 0.25rem;
    }
    
    .pagination-link {
        min-width: 36px;
        height: 36px;
        padding: 0.375rem 0.5rem;
        font-size: 0.875rem;
    }
}
</style>
