@extends('layouts.app')

@section('title', $cat['label'] . ' Interview Questions')

@section('content')
<div class="max-w-3xl mx-auto pb-12 space-y-6">

    {{-- Back --}}
    <a href="{{ route('interview.index') }}" class="inline-flex items-center gap-2 text-slate-400 hover:text-white transition-colors text-sm font-medium" data-aos="fade-down">
        <i class="fas fa-arrow-left"></i> Back to Categories
    </a>

    {{-- Header --}}
    <div class="glass rounded-2xl p-6 sm:p-8 relative overflow-hidden" data-aos="fade-up">
        <div class="absolute -right-12 -top-12 w-48 h-48 bg-rose-500/10 rounded-full blur-3xl"></div>
        <div class="flex items-center gap-4 relative z-10">
            <div class="w-14 h-14 rounded-2xl bg-rose-500/20 border border-rose-500/30 flex items-center justify-center text-rose-400 text-2xl">
                <i class="{{ $cat['icon'] }}"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-white">{{ $cat['label'] }}</h1>
                <p class="text-slate-400 text-sm">{{ count($cat['questions']) }} interview questions with expert tips</p>
            </div>
        </div>
    </div>

    {{-- Q&A Accordion --}}
    <div class="space-y-4" x-data="{ open: null }">
        @foreach($cat['questions'] as $idx => $item)
        <div class="glass-dark rounded-2xl overflow-hidden ring-1 ring-slate-700/50 hover:ring-rose-500/30 transition-all"
             data-aos="fade-up" data-aos-delay="{{ $idx * 80 }}">

            {{-- Question (trigger) --}}
            <button @click="open = open === {{ $idx }} ? null : {{ $idx }}"
                    class="w-full flex items-start gap-4 px-6 py-5 text-left group">
                <span class="shrink-0 w-8 h-8 rounded-lg bg-rose-500/20 text-rose-400 flex items-center justify-center text-sm font-extrabold mt-0.5">
                    {{ $idx + 1 }}
                </span>
                <span class="flex-1 text-white font-semibold text-sm sm:text-base leading-snug group-hover:text-rose-300 transition-colors">
                    {{ $item['q'] }}
                </span>
                <i class="fas fa-chevron-down text-slate-500 transition-transform duration-300 mt-1 shrink-0"
                   :class="open === {{ $idx }} ? 'rotate-180 text-rose-400' : ''"></i>
            </button>

            {{-- Answer tip (collapsible) --}}
            <div x-show="open === {{ $idx }}"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 style="display:none">
                <div class="px-6 pb-5 pt-0">
                    <div class="ml-12 p-4 rounded-xl bg-slate-800/60 border border-slate-700/50">
                        <div class="flex items-center gap-2 mb-2 text-amber-400 text-xs font-bold uppercase tracking-widest">
                            <i class="fas fa-lightbulb"></i> Expert Tip
                        </div>
                        <p class="text-slate-300 text-sm leading-relaxed">{{ $item['tip'] }}</p>
                    </div>
                </div>
            </div>

        </div>
        @endforeach
    </div>

    {{-- CTA --}}
    <div class="text-center pt-4" data-aos="fade-up">
        <p class="text-slate-500 text-sm mb-4">Want to practice another category?</p>
        <a href="{{ route('interview.index') }}"
           class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-rose-600 to-pink-600 hover:from-rose-500 hover:to-pink-500 text-white font-bold rounded-xl transition-all shadow-lg shadow-rose-500/20">
            <i class="fas fa-arrow-left"></i> Back to All Categories
        </a>
    </div>

</div>
@endsection
