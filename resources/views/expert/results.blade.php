@extends('layouts.app')

@section('title', 'Your Career Blueprint')

@section('content')
<div class="space-y-8">
    {{-- Success Message --}}
    @if(session('success'))
    <div class="bg-emerald-500/10 border border-emerald-500 text-emerald-400 p-5 rounded-2xl flex items-center gap-4 animate-fade-in">
        <div class="w-10 h-10 bg-emerald-500/20 rounded-xl flex items-center justify-center shrink-0">
            <i class="fas fa-check-circle text-emerald-400"></i>
        </div>
        <p class="font-medium">{{ session('success') }}</p>
    </div>
    @endif

    {{-- Header --}}
    <div class="text-center max-w-2xl mx-auto mb-4">
        <h1 class="text-4xl font-extrabold text-white mb-4">Your Career <span class="text-gradient">Blueprint</span></h1>
        <p class="text-slate-400">Our Certainty Factor engine analyzed your personality, skills, and interests against {{ $recommendations->count() > 0 ? 'all available positions' : 'our database' }}. Here are your top matches.</p>
    </div>

    @if($recommendations->count() > 0)
    {{-- Results Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach($recommendations as $rec)
        <div class="glass-dark overflow-hidden flex flex-col card-hover border-t-4 animate-fade-in {{ $loop->first ? 'border-blue-600 shadow-2xl shadow-blue-600/10' : ($loop->index < 3 ? 'border-indigo-500/50' : 'border-slate-700') }}"
             style="animation-delay: {{ $loop->index * 80 }}ms">

            {{-- Rank Badge --}}
            @if($loop->index < 3)
            <div class="px-4 py-2 bg-gradient-to-r {{ $loop->first ? 'from-blue-600 to-indigo-600' : ($loop->index === 1 ? 'from-indigo-600/50 to-purple-600/50' : 'from-slate-700 to-slate-600') }} flex items-center justify-between">
                <span class="text-[10px] font-bold text-white uppercase tracking-widest">
                    <i class="fas {{ $loop->first ? 'fa-trophy' : ($loop->index === 1 ? 'fa-medal' : 'fa-award') }} mr-1"></i>
                    #{{ $loop->iteration }} Best Match
                </span>
                <span class="text-xs font-bold text-white/80">CF {{ number_format($rec->score, 4) }}</span>
            </div>
            @endif

            <div class="p-7 flex-1">
                <div class="flex items-center justify-between mb-5">
                    <span class="px-3 py-1 rounded-full bg-blue-600/10 text-blue-500 text-[10px] font-bold uppercase tracking-widest">{{ $rec->job->category->name ?? 'General' }}</span>
                    <div class="text-right">
                        <span class="text-2xl font-bold text-white">{{ number_format($rec->confidence, 1) }}%</span>
                        <p class="text-[8px] text-slate-500 uppercase font-bold tracking-widest">Confidence</p>
                    </div>
                </div>

                {{-- Confidence Bar --}}
                <div class="w-full h-1.5 bg-slate-800 rounded-full overflow-hidden mb-5">
                    <div class="h-full rounded-full transition-all duration-1000 {{ $rec->confidence >= 70 ? 'bg-emerald-500' : ($rec->confidence >= 40 ? 'bg-blue-500' : 'bg-amber-500') }}"
                         style="width: {{ min($rec->confidence, 100) }}%"></div>
                </div>

                <h3 class="text-xl font-bold text-white mb-1.5">{{ $rec->job->title }}</h3>
                <p class="text-sm text-blue-400 mb-5"><i class="fas fa-building mr-1.5"></i> {{ $rec->job->company_name }}</p>

                <div class="p-4 rounded-xl bg-slate-800/50 border border-slate-700/50">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Why This Matches You</p>
                    <p class="text-sm text-slate-300 leading-relaxed">{{ $rec->explanation }}</p>
                </div>
            </div>

            <div class="p-5 bg-slate-800/30 border-t border-slate-800 flex items-center justify-between">
                <span class="text-xs font-medium text-slate-500"><i class="fas fa-map-marker-alt mr-1"></i> {{ $rec->job->location }}</span>
                <a href="{{ route('jobs.show', $rec->job->slug) }}" class="text-sm font-bold text-blue-500 hover:text-blue-400 transition-colors">
                    View Details <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
        @endforeach
    </div>
    @else
    {{-- Empty State --}}
    <div class="glass-dark p-16 text-center">
        <div class="w-20 h-20 bg-slate-800 rounded-3xl flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-search text-slate-600 text-3xl"></i>
        </div>
        <h3 class="text-xl font-bold text-white mb-3">No Matches Found</h3>
        <p class="text-slate-400 max-w-md mx-auto mb-8">We couldn't find strong matches with your current profile. Try adding more skills or selecting different interests.</p>
        <a href="{{ route('assessment.index') }}" class="btn-premium px-10">Retake Assessment</a>
    </div>
    @endif

    {{-- Bottom CTA --}}
    <div class="glass p-8 flex flex-col sm:flex-row items-center justify-between gap-6">
        <div>
            <h3 class="text-lg font-bold text-white mb-1">Want different results?</h3>
            <p class="text-slate-400 text-sm">Adjust your skill levels or interests for more accurate recommendations.</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('assessment.index') }}" class="px-6 py-3 rounded-xl bg-slate-800 hover:bg-slate-700 transition-all text-white font-bold text-sm">
                <i class="fas fa-redo mr-2"></i> Retake
            </a>
            <a href="{{ route('jobs.index') }}" class="btn-premium px-6 py-3 text-sm">
                <i class="fas fa-briefcase mr-2"></i> Browse All Jobs
            </a>
        </div>
    </div>
</div>
@endsection
