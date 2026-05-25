@extends('layouts.app')

@section('title', 'Matriks Keahlian')

@section('content')
<div class="max-w-7xl mx-auto space-y-8">

    {{-- ── Header ─────────────────────────────────────────────────────────────── --}}
    <div data-aos="fade-up">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-500/10 text-purple-400 text-xs font-bold tracking-widest uppercase mb-3 border border-purple-500/20">
            <i class="fas fa-layer-group"></i>
            Analisis Keahlian
        </div>
        <h1 class="text-3xl font-bold text-white mb-2">
            Matriks <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-indigo-400">Keahlian</span>
        </h1>
        <p class="text-slate-400 text-sm max-w-2xl">
            Bandingkan level keahlian Anda saat ini dengan yang dibutuhkan setiap jalur karir. Bar hijau adalah <strong class="text-slate-300">Anda</strong>; garis abu-abu adalah <strong class="text-slate-300">level target</strong>.
        </p>
    </div>

    {{-- ── Career Cards ────────────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
        @foreach($careers as $slug => $career)
        @php
            $totalRequired = array_sum($career['skills']);
            $totalUser     = 0;
            foreach ($career['skills'] as $skill => $req) {
                $totalUser += min($userSkills[$skill] ?? 0, $req);
            }
            $overallPct = $totalRequired > 0 ? round(($totalUser / $totalRequired) * 100) : 0;

            $colorMap = [
                'blue'    => ['ring' => 'ring-blue-500/30',    'bg'   => 'bg-blue-500',    'text' => 'text-blue-400',   'badge' => 'bg-blue-500/20 text-blue-300',   'icon' => 'bg-blue-500/20 text-blue-400'],
                'indigo'  => ['ring' => 'ring-indigo-500/30',  'bg'   => 'bg-indigo-500',  'text' => 'text-indigo-400', 'badge' => 'bg-indigo-500/20 text-indigo-300','icon' => 'bg-indigo-500/20 text-indigo-400'],
                'purple'  => ['ring' => 'ring-purple-500/30',  'bg'   => 'bg-purple-500',  'text' => 'text-purple-400', 'badge' => 'bg-purple-500/20 text-purple-300','icon' => 'bg-purple-500/20 text-purple-400'],
                'emerald' => ['ring' => 'ring-emerald-500/30', 'bg'   => 'bg-emerald-500', 'text' => 'text-emerald-400','badge' => 'bg-emerald-500/20 text-emerald-300','icon' => 'bg-emerald-500/20 text-emerald-400'],
            ];
            $c = $colorMap[$career['color']];
        @endphp

        <div class="glass-dark rounded-2xl p-6 ring-1 {{ $c['ring'] }} hover:shadow-xl transition-all duration-300" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 100 }}">

            {{-- Card header --}}
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl {{ $c['icon'] }} flex items-center justify-center text-xl">
                        <i class="{{ $career['icon'] }}"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-white">{{ $career['title'] }}</h2>
                        <p class="text-xs text-slate-500">{{ count($career['skills']) }} keahlian inti</p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="text-2xl font-extrabold {{ $c['text'] }}">{{ $overallPct }}%</span>
                    <p class="text-[10px] uppercase tracking-widest text-slate-500">kecocokan</p>
                </div>
            </div>

            {{-- Overall progress ring (simple bar) --}}
            <div class="mb-6">
                <div class="flex justify-between text-xs text-slate-400 mb-1">
                    <span>Kesiapan Keseluruhan</span>
                    <span>{{ $overallPct }}%</span>
                </div>
                <div class="w-full h-3 bg-slate-800 rounded-full overflow-hidden">
                    <div class="{{ $c['bg'] }} h-full rounded-full transition-all duration-700"
                         style="width: {{ $overallPct }}%"></div>
                </div>
            </div>

            {{-- Individual skills --}}
            <div class="space-y-3">
                @foreach($career['skills'] as $skill => $required)
                @php
                    $userLevel = $userSkills[$skill] ?? 0;
                    $gap       = max(0, $required - $userLevel);
                    $pctUser   = $userLevel;
                    $pctReq    = $required;
                @endphp
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-xs text-slate-300 font-medium">{{ $skill }}</span>
                        <div class="flex items-center gap-2 text-[10px]">
                            @if($gap > 0)
                                <span class="px-1.5 py-0.5 rounded bg-red-500/20 text-red-400 font-semibold">-{{ $gap }}%</span>
                            @else
                                <span class="px-1.5 py-0.5 rounded bg-emerald-500/20 text-emerald-400 font-semibold">✓ Siap</span>
                            @endif
                            <span class="text-slate-500">{{ $userLevel }}/{{ $required }}</span>
                        </div>
                    </div>
                    {{-- Stacked bar: user vs required --}}
                    <div class="relative w-full h-2 bg-slate-800 rounded-full overflow-hidden">
                        {{-- Required level (ghost) --}}
                        <div class="absolute top-0 left-0 h-full bg-slate-600/50 rounded-full"
                             style="width: {{ $pctReq }}%"></div>
                        {{-- User level --}}
                        <div class="{{ $c['bg'] }}/80 absolute top-0 left-0 h-full rounded-full transition-all duration-700"
                             style="width: {{ $pctUser }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- CTA --}}
            <div class="mt-6 pt-4 border-t border-slate-700/50">
                <a href="{{ route('roadmap.show', $slug) }}"
                   class="inline-flex items-center gap-2 text-xs font-semibold {{ $c['text'] }} hover:underline transition">
                    Lihat Peta Belajar untuk menutup gap <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ── Legend ──────────────────────────────────────────────────────────────── --}}
    <div class="flex flex-wrap gap-6 text-xs text-slate-400" data-aos="fade-up">
        <div class="flex items-center gap-2">
            <div class="w-4 h-2 rounded-full bg-slate-600/50"></div>
            <span>Level yang dibutuhkan</span>
        </div>
        <div class="flex items-center gap-2">
            <div class="w-4 h-2 rounded-full bg-blue-500/80"></div>
            <span>Level Anda saat ini</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-1.5 py-0.5 rounded bg-red-500/20 text-red-400 font-semibold">-N%</span>
            <span>Gap keahlian tersisa</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-1.5 py-0.5 rounded bg-emerald-500/20 text-emerald-400 font-semibold">✓ Siap</span>
            <span>Keahlian sudah terpenuhi</span>
        </div>
    </div>

</div>
@endsection
