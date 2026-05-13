@extends('layouts.app')

@section('title', 'Application Tracker')

@section('content')
<div class="max-w-full space-y-6">

    {{-- ── Header ─────────────────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4" data-aos="fade-up">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/10 text-amber-400 text-xs font-bold tracking-widest uppercase mb-3 border border-amber-500/20">
                <i class="fas fa-clipboard-list"></i>
                Kanban Board
            </div>
            <h1 class="text-3xl font-bold text-white mb-1">
                Application <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-400 to-orange-400">Tracker</span>
            </h1>
            <p class="text-slate-400 text-sm">Track every job application from wishlist to offer in one place.</p>
        </div>

        {{-- Stats summary --}}
        <div class="flex flex-wrap gap-3">
            @php $total = 0; foreach ($columns as $col) { $total += count($col['cards']); } @endphp
            <div class="glass-dark px-4 py-2 rounded-xl text-center">
                <p class="text-xl font-extrabold text-white">{{ $total }}</p>
                <p class="text-[10px] uppercase tracking-widest text-slate-500">Total</p>
            </div>
            @foreach ($columns as $key => $col)
            <div class="glass-dark px-4 py-2 rounded-xl text-center">
                <p class="text-xl font-extrabold text-white">{{ count($col['cards']) }}</p>
                <p class="text-[10px] uppercase tracking-widest text-slate-500">{{ $col['label'] }}</p>
            </div>
            @endforeach
        </div>
    </div>

    {{-- ── Kanban Board ────────────────────────────────────────────────────────── --}}
    <div class="overflow-x-auto pb-4">
        <div class="flex gap-5 min-w-max">

            @foreach($columns as $key => $col)
            @php
                $colorMap = [
                    'slate'   => ['header' => 'bg-slate-700/50 text-slate-300',   'dot' => 'bg-slate-400',   'badge' => 'bg-slate-600 text-slate-200',   'ring' => 'ring-slate-700/50'],
                    'blue'    => ['header' => 'bg-blue-600/20 text-blue-300',     'dot' => 'bg-blue-400',    'badge' => 'bg-blue-600/30 text-blue-300',   'ring' => 'ring-blue-500/20'],
                    'amber'   => ['header' => 'bg-amber-500/20 text-amber-300',   'dot' => 'bg-amber-400',   'badge' => 'bg-amber-500/30 text-amber-300', 'ring' => 'ring-amber-500/20'],
                    'emerald' => ['header' => 'bg-emerald-500/20 text-emerald-300','dot' => 'bg-emerald-400','badge' => 'bg-emerald-500/30 text-emerald-300','ring'=> 'ring-emerald-500/20'],
                    'red'     => ['header' => 'bg-red-500/20 text-red-300',       'dot' => 'bg-red-400',     'badge' => 'bg-red-500/30 text-red-300',     'ring' => 'ring-red-500/20'],
                ];
                $c = $colorMap[$col['color']];
            @endphp

            <div class="w-72 flex flex-col gap-3" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 80 }}">

                {{-- Column header --}}
                <div class="flex items-center justify-between px-4 py-2.5 rounded-xl {{ $c['header'] }} ring-1 {{ $c['ring'] }}">
                    <div class="flex items-center gap-2 font-bold text-sm">
                        <i class="{{ $col['icon'] }}"></i>
                        {{ $col['label'] }}
                    </div>
                    <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ $c['badge'] }}">
                        {{ count($col['cards']) }}
                    </span>
                </div>

                {{-- Cards --}}
                <div class="flex flex-col gap-3">
                    @forelse($col['cards'] as $card)
                    <div class="glass-dark rounded-2xl p-4 ring-1 {{ $c['ring'] }} hover:-translate-y-1 hover:shadow-lg transition-all duration-200 group cursor-pointer">

                        {{-- Company logo + name --}}
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-600 to-indigo-700 flex items-center justify-center text-white font-extrabold text-sm shadow-md shadow-indigo-500/20 shrink-0">
                                {{ $card['logo'] }}
                            </div>
                            <div class="overflow-hidden">
                                <p class="text-sm font-bold text-white truncate group-hover:text-blue-400 transition-colors">{{ $card['company'] }}</p>
                                <p class="text-[11px] text-slate-400 truncate">{{ $card['role'] }}</p>
                            </div>
                        </div>

                        {{-- Meta --}}
                        <div class="flex flex-wrap gap-2">
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-slate-800 text-slate-400 text-[10px]">
                                <i class="fas fa-map-marker-alt"></i> {{ $card['location'] }}
                            </span>
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-slate-800 text-slate-400 text-[10px]">
                                <i class="fas fa-money-bill-wave"></i> {{ $card['salary'] }}
                            </span>
                        </div>

                        {{-- Status dot --}}
                        <div class="flex items-center gap-1.5 mt-3 pt-3 border-t border-slate-700/50">
                            <span class="w-2 h-2 rounded-full {{ $c['dot'] }}"></span>
                            <span class="text-[10px] text-slate-500 uppercase tracking-widest font-semibold">{{ $col['label'] }}</span>
                        </div>
                    </div>
                    @empty
                    <div class="flex flex-col items-center justify-center py-10 text-slate-600 border-2 border-dashed border-slate-700/60 rounded-2xl">
                        <i class="fas fa-inbox text-3xl mb-2"></i>
                        <p class="text-xs">No applications yet</p>
                    </div>
                    @endforelse
                </div>

            </div>
            @endforeach

        </div>
    </div>

    {{-- ── Safelist for Tailwind JIT ───────────────────────────────────────────── --}}
    <div class="hidden">
        <div class="bg-slate-700/50 text-slate-300 bg-slate-600 text-slate-200 ring-slate-700/50 bg-slate-400"></div>
        <div class="bg-blue-600/20 text-blue-300 bg-blue-600/30 ring-blue-500/20 bg-blue-400"></div>
        <div class="bg-amber-500/20 text-amber-300 bg-amber-500/30 ring-amber-500/20 bg-amber-400"></div>
        <div class="bg-emerald-500/20 text-emerald-300 bg-emerald-500/30 ring-emerald-500/20 bg-emerald-400"></div>
        <div class="bg-red-500/20 text-red-300 bg-red-500/30 ring-red-500/20 bg-red-400"></div>
    </div>

</div>
@endsection
