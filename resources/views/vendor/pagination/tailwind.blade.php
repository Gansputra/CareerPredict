@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex flex-col items-center gap-6">

        {{-- Info Text --}}
        <div class="text-sm text-slate-500 font-medium tracking-wide">
            @if ($paginator->firstItem())
                Showing <span class="text-blue-400 font-bold">{{ $paginator->firstItem() }}</span>
                to <span class="text-blue-400 font-bold">{{ $paginator->lastItem() }}</span>
                of <span class="text-white font-bold">{{ $paginator->total() }}</span> results
            @else
                No results found
            @endif
        </div>

        {{-- Pagination Controls --}}
        <div class="flex items-center gap-2">

            {{-- Previous Button --}}
            @if ($paginator->onFirstPage())
                <span class="w-10 h-10 flex items-center justify-center rounded-xl bg-slate-800/50 text-slate-600 cursor-not-allowed" aria-disabled="true">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                   class="w-10 h-10 flex items-center justify-center rounded-xl bg-slate-800 text-slate-300 hover:bg-blue-600 hover:text-white hover:shadow-lg hover:shadow-blue-600/30 transition-all duration-300"
                   aria-label="{{ __('pagination.previous') }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
                </a>
            @endif

            {{-- Page Numbers --}}
            @foreach ($elements as $element)
                {{-- Dots Separator --}}
                @if (is_string($element))
                    <span class="w-10 h-10 flex items-center justify-center text-slate-600 text-sm font-bold tracking-widest">{{ $element }}</span>
                @endif

                {{-- Page Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            {{-- Active Page --}}
                            <span aria-current="page"
                                  class="w-10 h-10 flex items-center justify-center rounded-xl bg-blue-600 text-white text-sm font-bold shadow-lg shadow-blue-600/40 scale-110 cursor-default">
                                {{ $page }}
                            </span>
                        @else
                            {{-- Normal Page --}}
                            <a href="{{ $url }}"
                               class="w-10 h-10 flex items-center justify-center rounded-xl bg-slate-800/80 text-slate-400 text-sm font-medium hover:bg-slate-700 hover:text-white transition-all duration-300"
                               aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Button --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                   class="w-10 h-10 flex items-center justify-center rounded-xl bg-slate-800 text-slate-300 hover:bg-blue-600 hover:text-white hover:shadow-lg hover:shadow-blue-600/30 transition-all duration-300"
                   aria-label="{{ __('pagination.next') }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                </a>
            @else
                <span class="w-10 h-10 flex items-center justify-center rounded-xl bg-slate-800/50 text-slate-600 cursor-not-allowed" aria-disabled="true">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                </span>
            @endif

        </div>
    </nav>
@endif
