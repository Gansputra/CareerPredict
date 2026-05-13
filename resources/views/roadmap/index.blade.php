@extends('layouts.app')

@section('title', 'Learning Roadmap')

@section('content')
<div class="max-w-7xl mx-auto space-y-8">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4" data-aos="fade-up">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/10 text-blue-400 text-xs font-bold tracking-widest uppercase mb-3 border border-blue-500/20">
                <i class="fas fa-map-signs"></i>
                Career Paths
            </div>
            <h1 class="text-3xl font-bold text-white mb-2">Learning <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-indigo-400">Roadmaps</span></h1>
            <p class="text-slate-400 text-sm max-w-2xl">
                Choose a career path below to view a step-by-step guide on the skills and technologies you need to master to achieve your dream job.
            </p>
        </div>
    </div>

    <!-- Roadmaps Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @foreach($roadmaps as $slug => $roadmap)
        <div class="glass-dark rounded-2xl p-1 hover:-translate-y-2 transition-all duration-300 group" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 100 }}">
            <div class="bg-slate-800/50 rounded-xl p-6 h-full flex flex-col relative overflow-hidden">
                
                <!-- Background decoration -->
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-{{ $roadmap['color'] }}-500/10 rounded-full blur-2xl group-hover:bg-{{ $roadmap['color'] }}-500/20 transition-all"></div>
                
                <div class="flex items-start justify-between mb-4 relative z-10">
                    <div class="w-12 h-12 rounded-xl bg-{{ $roadmap['color'] }}-500/20 border border-{{ $roadmap['color'] }}-500/30 flex items-center justify-center text-{{ $roadmap['color'] }}-400 text-xl shadow-lg shadow-{{ $roadmap['color'] }}-500/10">
                        <i class="{{ $roadmap['icon'] }}"></i>
                    </div>
                    <span class="px-3 py-1 bg-slate-900/50 rounded-full border border-slate-700 text-xs font-medium text-slate-300">
                        {{ count($roadmap['steps']) }} Steps
                    </span>
                </div>
                
                <h3 class="text-xl font-bold text-white mb-2 relative z-10 group-hover:text-{{ $roadmap['color'] }}-400 transition-colors">{{ $roadmap['title'] }}</h3>
                <p class="text-sm text-slate-400 mb-6 flex-1 relative z-10 line-clamp-2">
                    {{ $roadmap['description'] }}
                </p>
                
                <div class="grid grid-cols-2 gap-4 mb-6 relative z-10">
                    <div class="bg-slate-900/50 rounded-lg p-3 border border-slate-700/50">
                        <p class="text-[10px] uppercase tracking-wider text-slate-500 font-bold mb-1">Estimated Time</p>
                        <p class="text-sm font-semibold text-white flex items-center gap-2">
                            <i class="fas fa-clock text-slate-400"></i> {{ $roadmap['estimated_time'] }}
                        </p>
                    </div>
                    <div class="bg-slate-900/50 rounded-lg p-3 border border-slate-700/50">
                        <p class="text-[10px] uppercase tracking-wider text-slate-500 font-bold mb-1">Difficulty</p>
                        <p class="text-sm font-semibold text-white flex items-center gap-2">
                            <i class="fas fa-layer-group text-slate-400"></i> {{ $roadmap['difficulty'] }}
                        </p>
                    </div>
                </div>
                
                <a href="{{ route('roadmap.show', $slug) }}" class="w-full py-3 px-4 bg-slate-700 hover:bg-{{ $roadmap['color'] }}-600 text-white text-sm font-bold rounded-xl transition-all text-center flex items-center justify-center gap-2 relative z-10">
                    View Roadmap <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
        @endforeach
    </div>

</div>

<div class="hidden">
    <!-- Safelist for Tailwind JIT -->
    <div class="bg-blue-500/10 bg-indigo-500/10 bg-purple-500/10 bg-emerald-500/10"></div>
    <div class="bg-blue-500/20 bg-indigo-500/20 bg-purple-500/20 bg-emerald-500/20"></div>
    <div class="text-blue-400 text-indigo-400 text-purple-400 text-emerald-400"></div>
    <div class="group-hover:text-blue-400 group-hover:text-indigo-400 group-hover:text-purple-400 group-hover:text-emerald-400"></div>
    <div class="group-hover:bg-blue-500/20 group-hover:bg-indigo-500/20 group-hover:bg-purple-500/20 group-hover:bg-emerald-500/20"></div>
    <div class="border-blue-500/30 border-indigo-500/30 border-purple-500/30 border-emerald-500/30"></div>
    <div class="shadow-blue-500/10 shadow-indigo-500/10 shadow-purple-500/10 shadow-emerald-500/10"></div>
    <div class="hover:bg-blue-600 hover:bg-indigo-600 hover:bg-purple-600 hover:bg-emerald-600"></div>
</div>
@endsection
