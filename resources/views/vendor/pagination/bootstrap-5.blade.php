@if ($paginator->hasPages())
    <nav class="d-flex flex-column flex-sm-row align-items-center justify-content-between gap-3 w-100" aria-label="Page navigation">
        {{-- Results Summary --}}
        <div class="pagination-info text-muted">
            @if (app()->getLocale() === 'km')
                បង្ហាញ <span class="pagination-highlight">{{ $paginator->firstItem() }}</span> ដល់ <span class="pagination-highlight">{{ $paginator->lastItem() }}</span> នៃ <span class="pagination-highlight">{{ $paginator->total() }}</span> លទ្ធផល
            @else
                Showing <span class="pagination-highlight">{{ $paginator->firstItem() }}</span> to <span class="pagination-highlight">{{ $paginator->lastItem() }}</span> of <span class="pagination-highlight">{{ $paginator->total() }}</span> results
            @endif
        </div>

        {{-- Pagination Buttons --}}
        <div>
            <ul class="pagination mb-0">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <li class="page-item disabled" aria-disabled="true" aria-label="{{ app()->getLocale() === 'km' ? 'ទំព័រមុន' : 'Previous' }}">
                        <span class="page-link" aria-hidden="true">
                            <i class="bi bi-chevron-left"></i>
                        </span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="{{ app()->getLocale() === 'km' ? 'ទំព័រមុន' : 'Previous' }}">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                    </li>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <li class="page-item disabled separator" aria-disabled="true">
                            <span class="page-link">{{ $element }}</span>
                        </li>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <li class="page-item active" aria-current="page">
                                    <span class="page-link">{{ $page }}</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                </li>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="{{ app()->getLocale() === 'km' ? 'ទំព័របន្ទាប់' : 'Next' }}">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                @else
                    <li class="page-item disabled" aria-disabled="true" aria-label="{{ app()->getLocale() === 'km' ? 'ទំព័របន្ទាប់' : 'Next' }}">
                        <span class="page-link" aria-hidden="true">
                            <i class="bi bi-chevron-right"></i>
                        </span>
                    </li>
                @endif
            </ul>
        </div>
    </nav>
@endif
