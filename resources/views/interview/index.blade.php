@extends('layouts.app')

@section('title', 'Interview Simulator')

@section('content')
<div class="max-w-6xl mx-auto space-y-8">

    {{-- ── Header ─────────────────────────────────────────────────────────────── --}}
    <div data-aos="fade-up">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-rose-500/10 text-rose-400 text-xs font-bold tracking-widest uppercase mb-3 border border-rose-500/20">
            <i class="fas fa-microphone-lines"></i>
            Interview Prep
        </div>
        <h1 class="text-3xl font-bold text-white mb-2">
            Interview <span class="text-transparent bg-clip-text bg-gradient-to-r from-rose-400 to-pink-400">Simulator</span>
        </h1>
        <p class="text-slate-400 text-sm max-w-2xl">
            Practice the most common interview questions for your target career. Click a category to reveal the questions with expert answer tips.
        </p>
    </div>

    {{-- ── Category Cards ──────────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
        @php
            $colorMap = [
                'slate'   => ['border' => 'border-slate-600/50',   'icon'  => 'bg-slate-700 text-slate-300',        'text' => 'text-slate-300',   'badge' => 'bg-slate-700 text-slate-300',       'btn' => 'from-slate-600 to-slate-700 hover:from-slate-500 hover:to-slate-600'],
                'blue'    => ['border' => 'border-blue-500/30',    'icon'  => 'bg-blue-500/20 text-blue-400',       'text' => 'text-blue-400',    'badge' => 'bg-blue-500/20 text-blue-300',      'btn' => 'from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500'],
                'indigo'  => ['border' => 'border-indigo-500/30',  'icon'  => 'bg-indigo-500/20 text-indigo-400',   'text' => 'text-indigo-400',  'badge' => 'bg-indigo-500/20 text-indigo-300',  'btn' => 'from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500'],
                'purple'  => ['border' => 'border-purple-500/30',  'icon'  => 'bg-purple-500/20 text-purple-400',   'text' => 'text-purple-400',  'badge' => 'bg-purple-500/20 text-purple-300',  'btn' => 'from-purple-600 to-pink-600 hover:from-purple-500 hover:to-pink-500'],
                'emerald' => ['border' => 'border-emerald-500/30', 'icon'  => 'bg-emerald-500/20 text-emerald-400', 'text' => 'text-emerald-400', 'badge' => 'bg-emerald-500/20 text-emerald-300', 'btn' => 'from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500'],
            ];
        @endphp

        @foreach($categoryMeta as $key => $cat)
        @php $c = $colorMap[$cat['color']]; @endphp
        <div class="glass-dark rounded-2xl p-6 border {{ $c['border'] }} hover:-translate-y-2 transition-all duration-300 flex flex-col group"
             data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 80 }}">

            <div class="flex items-start justify-between mb-5">
                <div class="w-12 h-12 rounded-xl {{ $c['icon'] }} flex items-center justify-center text-2xl">
                    <i class="{{ $cat['icon'] }}"></i>
                </div>
                <span class="px-2.5 py-1 rounded-lg text-xs font-bold {{ $c['badge'] }}">
                    {{ $cat['count'] }} Q&A
                </span>
            </div>

            <h2 class="text-lg font-bold text-white mb-1 group-hover:{{ $c['text'] }} transition-colors">{{ $cat['label'] }}</h2>
            <p class="text-sm text-slate-500 flex-1 mb-5">
                Practice {{ $cat['count'] }} carefully crafted questions with expert tips for the {{ $cat['label'] }} interview.
            </p>

            <a href="{{ route('interview.show', $key) }}"
               class="w-full py-2.5 px-4 bg-gradient-to-r {{ $c['btn'] }} text-white text-sm font-bold rounded-xl transition-all text-center shadow-lg">
                Start Practice <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        @endforeach
    </div>

    {{-- ── Tip banner ───────────────────────────────────────────────────────────── --}}
    <div class="glass rounded-2xl p-6 flex gap-4 items-start" data-aos="fade-up">
        <div class="w-10 h-10 shrink-0 bg-amber-500/20 rounded-xl flex items-center justify-center text-amber-400">
            <i class="fas fa-lightbulb"></i>
        </div>
        <div>
            <p class="font-semibold text-white mb-1">Pro Tip: Use the STAR Method</p>
            <p class="text-sm text-slate-400 leading-relaxed">
                For behavioural questions, structure your answer using <strong class="text-slate-300">S</strong>ituation, <strong class="text-slate-300">T</strong>ask, <strong class="text-slate-300">A</strong>ction, and <strong class="text-slate-300">R</strong>esult. This keeps your answer concise, relevant, and impactful.
            </p>
        </div>
    </div>

    {{-- Safelist --}}
    <div class="hidden">
        <div class="border-slate-600/50 bg-slate-700 text-slate-300 from-slate-600 to-slate-700 hover:from-slate-500 hover:to-slate-600"></div>
        <div class="border-blue-500/30 bg-blue-500/20 text-blue-400 text-blue-300 from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500"></div>
        <div class="border-indigo-500/30 bg-indigo-500/20 text-indigo-400 text-indigo-300 from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500"></div>
        <div class="border-purple-500/30 bg-purple-500/20 text-purple-400 text-purple-300 from-purple-600 to-pink-600 hover:from-purple-500 hover:to-pink-500"></div>
        <div class="border-emerald-500/30 bg-emerald-500/20 text-emerald-400 text-emerald-300 from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500"></div>
    </div>
</div>
@endsection
