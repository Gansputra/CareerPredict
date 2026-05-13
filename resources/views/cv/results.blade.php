@extends('layouts.app')

@section('title', 'CV Analysis Results')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-white mb-1">CV Analysis <span class="text-gradient">Report</span></h1>
            <p class="text-slate-400 text-sm">Your personalized career intelligence report powered by Certainty Factor analysis.</p>
        </div>
        <a href="{{ route('cv.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-sm transition-all shrink-0">
            <i class="fas fa-redo mr-2"></i> Analyze Another CV
        </a>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="glass-dark p-5 animate-fade-in" style="animation-delay: 0ms">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 bg-blue-600/10 rounded-xl flex items-center justify-center">
                    <i class="fas fa-code text-blue-400 text-sm"></i>
                </div>
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Skills Found</span>
            </div>
            <p class="text-3xl font-extrabold text-white">{{ count($detectedSkills) }}</p>
        </div>
        <div class="glass-dark p-5 animate-fade-in" style="animation-delay: 80ms">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 bg-purple-600/10 rounded-xl flex items-center justify-center">
                    <i class="fas fa-heart text-purple-400 text-sm"></i>
                </div>
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Interests</span>
            </div>
            <p class="text-3xl font-extrabold text-white">{{ count($detectedInterests) }}</p>
        </div>
        <div class="glass-dark p-5 animate-fade-in" style="animation-delay: 160ms">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 bg-emerald-600/10 rounded-xl flex items-center justify-center">
                    <i class="fas fa-briefcase text-emerald-400 text-sm"></i>
                </div>
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Job Matches</span>
            </div>
            <p class="text-3xl font-extrabold text-white">{{ count($recommendations) }}</p>
        </div>
        <div class="glass-dark p-5 animate-fade-in" style="animation-delay: 240ms">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 bg-amber-600/10 rounded-xl flex items-center justify-center">
                    <i class="fas fa-file-lines text-amber-400 text-sm"></i>
                </div>
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Words Scanned</span>
            </div>
            <p class="text-3xl font-extrabold text-white">{{ number_format($cvInfo['word_count']) }}</p>
        </div>
    </div>

    <!-- Skills & Career Fit -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Detected Skills -->
        <div class="glass-dark p-6 lg:p-8">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-white"><i class="fas fa-microchip text-blue-400 mr-2"></i> Detected Skills</h3>
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ count($detectedSkills) }} found</span>
            </div>
            @if(count($detectedSkills) > 0)
            <div class="space-y-3">
                @foreach($detectedSkills as $skill)
                <div class="flex items-center justify-between p-3 rounded-xl bg-slate-800/50 hover:bg-slate-800 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 rounded-full {{ $skill['confidence'] >= 0.8 ? 'bg-emerald-400' : ($skill['confidence'] >= 0.7 ? 'bg-blue-400' : 'bg-amber-400') }}"></div>
                        <span class="text-sm font-medium text-white">{{ $skill['name'] }}</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-20 h-1.5 bg-slate-700 rounded-full overflow-hidden hidden sm:block">
                            <div class="h-full rounded-full {{ $skill['confidence'] >= 0.8 ? 'bg-emerald-400' : ($skill['confidence'] >= 0.7 ? 'bg-blue-400' : 'bg-amber-400') }}"
                                 style="width: {{ $skill['confidence'] * 100 }}%"></div>
                        </div>
                        <span class="text-xs font-bold {{ $skill['confidence'] >= 0.8 ? 'text-emerald-400' : ($skill['confidence'] >= 0.7 ? 'text-blue-400' : 'text-amber-400') }}">
                            {{ round($skill['confidence'] * 100) }}%
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-8">
                <i class="fas fa-search text-slate-700 text-3xl mb-3"></i>
                <p class="text-slate-500 text-sm">No skills detected. Try a more detailed CV.</p>
            </div>
            @endif
        </div>

        <!-- Career Category Fit -->
        <div class="glass-dark p-6 lg:p-8">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-white"><i class="fas fa-chart-pie text-purple-400 mr-2"></i> Career Fit Analysis</h3>
            </div>
            @if(count($careerCategories) > 0)
            <div class="space-y-4">
                @foreach($careerCategories as $cat)
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-white">{{ $cat['name'] }}</span>
                        <span class="text-xs font-bold {{ $cat['score'] >= 50 ? 'text-emerald-400' : ($cat['score'] >= 25 ? 'text-blue-400' : 'text-slate-400') }}">
                            {{ $cat['score'] }}% fit
                        </span>
                    </div>
                    <div class="w-full h-2.5 bg-slate-800 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-1000 {{ $cat['score'] >= 50 ? 'bg-gradient-to-r from-emerald-500 to-teal-400' : ($cat['score'] >= 25 ? 'bg-gradient-to-r from-blue-500 to-indigo-400' : 'bg-slate-600') }}"
                             style="width: {{ $cat['score'] }}%"></div>
                    </div>
                    <p class="text-[10px] text-slate-500 mt-1">{{ $cat['matched'] }} of {{ $cat['total'] }} key skills matched</p>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-8">
                <i class="fas fa-chart-bar text-slate-700 text-3xl mb-3"></i>
                <p class="text-slate-500 text-sm">Not enough data for career analysis.</p>
            </div>
            @endif

            @if(count($detectedInterests) > 0)
            <div class="mt-8 pt-6 border-t border-slate-800">
                <h4 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-3">Detected Interests</h4>
                <div class="flex flex-wrap gap-2">
                    @foreach($detectedInterests as $interest)
                    <span class="px-3 py-1.5 rounded-full bg-purple-500/10 text-purple-400 text-xs font-bold">{{ $interest['name'] }}</span>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Job Recommendations -->
    <div>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-white">Recommended Jobs</h2>
                <p class="text-slate-400 text-sm">Based on your CV analysis, ranked by Certainty Factor score.</p>
            </div>
        </div>

        @if(count($recommendations) > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($recommendations as $rec)
            <div class="glass-dark overflow-hidden flex flex-col card-hover border-t-4 animate-fade-in {{ $loop->first ? 'border-blue-600 shadow-2xl shadow-blue-600/10' : ($loop->index < 3 ? 'border-indigo-500/50' : 'border-slate-700') }}"
                 style="animation-delay: {{ $loop->index * 60 }}ms">

                @if($loop->index < 3)
                <div class="px-4 py-2 bg-gradient-to-r {{ $loop->first ? 'from-blue-600 to-indigo-600' : ($loop->index === 1 ? 'from-indigo-600/50 to-purple-600/50' : 'from-slate-700 to-slate-600') }} flex items-center justify-between">
                    <span class="text-[10px] font-bold text-white uppercase tracking-widest">
                        <i class="fas {{ $loop->first ? 'fa-trophy' : ($loop->index === 1 ? 'fa-medal' : 'fa-award') }} mr-1"></i>
                        #{{ $loop->iteration }} Best Match
                    </span>
                    <span class="text-xs font-bold text-white/80">CF {{ $rec['score'] }}</span>
                </div>
                @endif

                <div class="p-6 flex-1">
                    <div class="flex items-center justify-between mb-4">
                        <span class="px-2.5 py-1 rounded-full bg-blue-600/10 text-blue-500 text-[10px] font-bold uppercase tracking-widest">{{ $rec['job']->category->name ?? 'General' }}</span>
                        <div class="text-right">
                            <span class="text-xl font-bold text-white">{{ $rec['confidence'] }}%</span>
                            <p class="text-[8px] text-slate-500 uppercase font-bold">Match</p>
                        </div>
                    </div>

                    <div class="w-full h-1 bg-slate-800 rounded-full overflow-hidden mb-4">
                        <div class="h-full rounded-full {{ $rec['confidence'] >= 60 ? 'bg-emerald-500' : ($rec['confidence'] >= 30 ? 'bg-blue-500' : 'bg-amber-500') }}"
                             style="width: {{ min($rec['confidence'], 100) }}%"></div>
                    </div>

                    <h3 class="text-lg font-bold text-white mb-1">{{ $rec['job']->title }}</h3>
                    <p class="text-sm text-blue-400 mb-3"><i class="fas fa-building mr-1"></i> {{ $rec['job']->company_name }}</p>

                    <!-- Matched Skills Tags -->
                    @if(count($rec['matched_skills']) > 0)
                    <div class="flex flex-wrap gap-1.5 mb-4">
                        @foreach($rec['matched_skills'] as $ms)
                        <span class="px-2 py-0.5 rounded-md bg-emerald-500/10 text-emerald-400 text-[10px] font-bold">{{ $ms }}</span>
                        @endforeach
                    </div>
                    @endif

                    <div class="p-3 rounded-xl bg-slate-800/50 border border-slate-700/50">
                        <p class="text-xs text-slate-300 leading-relaxed">{{ $rec['explanation'] }}</p>
                    </div>
                </div>

                <div class="p-4 bg-slate-800/30 border-t border-slate-800 flex items-center justify-between">
                    <span class="text-xs text-slate-500"><i class="fas fa-map-marker-alt mr-1"></i> {{ $rec['job']->location }}</span>
                    <a href="{{ route('jobs.show', $rec['job']->slug) }}" class="text-sm font-bold text-blue-500 hover:text-blue-400 transition-colors">
                        View <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="glass-dark p-16 text-center">
            <div class="w-20 h-20 bg-slate-800 rounded-3xl flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-search text-slate-600 text-3xl"></i>
            </div>
            <h3 class="text-xl font-bold text-white mb-3">No Strong Matches Found</h3>
            <p class="text-slate-400 max-w-md mx-auto mb-6">Your CV didn't produce strong matches with current job listings. Try uploading a more detailed CV or browse jobs manually.</p>
            <a href="{{ route('jobs.index') }}" class="btn-premium px-8">Browse All Jobs</a>
        </div>
        @endif
    </div>

    <!-- Bottom CTA -->
    <div class="glass p-6 sm:p-8 flex flex-col sm:flex-row items-center justify-between gap-6">
        <div>
            <h3 class="text-lg font-bold text-white mb-1">Want deeper insights?</h3>
            <p class="text-slate-400 text-sm">Take the full Career DNA Test for a comprehensive personality-based analysis.</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('cv.index') }}" class="px-5 py-3 rounded-xl bg-slate-800 hover:bg-slate-700 transition-all text-white font-bold text-sm">
                <i class="fas fa-upload mr-2"></i> Re-upload
            </a>
            <a href="{{ route('assessment.index') }}" class="btn-premium px-5 py-3 text-sm">
                <i class="fas fa-dna mr-2"></i> Career DNA Test
            </a>
        </div>
    </div>
</div>
@endsection
