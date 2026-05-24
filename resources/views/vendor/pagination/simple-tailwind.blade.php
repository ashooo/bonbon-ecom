@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between gap-3 rounded-2xl border border-[#E9C7D4] bg-[linear-gradient(180deg,#FFFFFF_0%,#FBF2F6_100%)] px-4 py-3 shadow-[0_8px_24px_rgba(77,46,56,0.08)]">
        @if ($paginator->onFirstPage())
            <span class="inline-flex h-10 items-center justify-center rounded-xl border border-[#ECD8E0] bg-[#FBF2F6] px-4 text-sm font-medium text-[#BFA5AE]" aria-disabled="true">{!! __('pagination.previous') !!}</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex h-10 items-center justify-center rounded-xl border border-[#E9C7D4] bg-white px-4 text-sm font-medium text-[#8F6172] transition hover:bg-[#FBEAF1] hover:text-[#4D2E38]">{!! __('pagination.previous') !!}</a>
        @endif

        <span class="text-sm font-medium text-[#8F6172]">Page <span class="font-semibold text-[#4D2E38]">{{ $paginator->currentPage() }}</span></span>

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex h-10 items-center justify-center rounded-xl border border-[#E9C7D4] bg-white px-4 text-sm font-medium text-[#8F6172] transition hover:bg-[#FBEAF1] hover:text-[#4D2E38]">{!! __('pagination.next') !!}</a>
        @else
            <span class="inline-flex h-10 items-center justify-center rounded-xl border border-[#ECD8E0] bg-[#FBF2F6] px-4 text-sm font-medium text-[#BFA5AE]" aria-disabled="true">{!! __('pagination.next') !!}</span>
        @endif
    </nav>
@endif
