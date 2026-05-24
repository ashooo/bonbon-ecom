@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between gap-3 rounded-2xl border border-[#E9C7D4] bg-[linear-gradient(180deg,#FFFFFF_0%,#FBF2F6_100%)] px-4 py-3 shadow-[0_8px_24px_rgba(77,46,56,0.08)]">
        <div class="hidden sm:block text-sm font-medium text-[#8F6172]">
            Showing
            <span class="font-semibold text-[#4D2E38]">{{ $paginator->firstItem() }}</span>
            to
            <span class="font-semibold text-[#4D2E38]">{{ $paginator->lastItem() }}</span>
            of
            <span class="font-semibold text-[#4D2E38]">{{ $paginator->total() }}</span>
            results
        </div>

        <div class="flex flex-1 items-center justify-between sm:justify-end gap-2">
            @if ($paginator->onFirstPage())
                <span class="inline-flex h-10 min-w-[2.5rem] items-center justify-center rounded-xl border border-[#ECD8E0] bg-[#FBF2F6] px-3 text-[#BFA5AE]" aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M12.78 15.78a.75.75 0 0 1-1.06 0l-5.25-5.25a.75.75 0 0 1 0-1.06l5.25-5.25a.75.75 0 1 1 1.06 1.06L8.06 10l4.72 4.72a.75.75 0 0 1 0 1.06Z" clip-rule="evenodd" /></svg>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex h-10 min-w-[2.5rem] items-center justify-center rounded-xl border border-[#E9C7D4] bg-white px-3 text-[#8F6172] transition hover:bg-[#FBEAF1] hover:text-[#4D2E38]" aria-label="{{ __('pagination.previous') }}">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M12.78 15.78a.75.75 0 0 1-1.06 0l-5.25-5.25a.75.75 0 0 1 0-1.06l5.25-5.25a.75.75 0 1 1 1.06 1.06L8.06 10l4.72 4.72a.75.75 0 0 1 0 1.06Z" clip-rule="evenodd" /></svg>
                </a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="hidden sm:inline-flex h-10 min-w-[2.5rem] items-center justify-center rounded-xl border border-[#ECD8E0] bg-[#FBF2F6] px-3 text-sm text-[#8A6A76]">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" class="inline-flex h-10 min-w-[2.5rem] items-center justify-center rounded-xl border border-[#B66880] bg-[#C47A90] px-3 text-sm font-semibold text-white shadow-[0_6px_16px_rgba(196,122,144,0.35)]">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="inline-flex h-10 min-w-[2.5rem] items-center justify-center rounded-xl border border-[#E9C7D4] bg-white px-3 text-sm font-medium text-[#8F6172] transition hover:bg-[#FBEAF1] hover:text-[#4D2E38]" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex h-10 min-w-[2.5rem] items-center justify-center rounded-xl border border-[#E9C7D4] bg-white px-3 text-[#8F6172] transition hover:bg-[#FBEAF1] hover:text-[#4D2E38]" aria-label="{{ __('pagination.next') }}">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M7.22 4.22a.75.75 0 0 1 1.06 0l5.25 5.25a.75.75 0 0 1 0 1.06l-5.25 5.25a.75.75 0 1 1-1.06-1.06L11.94 10 7.22 5.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" /></svg>
                </a>
            @else
                <span class="inline-flex h-10 min-w-[2.5rem] items-center justify-center rounded-xl border border-[#ECD8E0] bg-[#FBF2F6] px-3 text-[#BFA5AE]" aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M7.22 4.22a.75.75 0 0 1 1.06 0l5.25 5.25a.75.75 0 0 1 0 1.06l-5.25 5.25a.75.75 0 1 1-1.06-1.06L11.94 10 7.22 5.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" /></svg>
                </span>
            @endif
        </div>
    </nav>
@endif
