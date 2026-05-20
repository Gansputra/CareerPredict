@extends('layouts.app')

@section('title', $roadmap['title'] . ' - Peta Belajar')

@section('content')
<div class="max-w-4xl mx-auto pb-12">
    
    <!-- Back Button & Header -->
    <div class="mb-8" data-aos="fade-down">
        <a href="{{ route('roadmap.index') }}" class="inline-flex items-center gap-2 text-slate-400 hover:text-white transition-colors mb-6 text-sm font-medium">
            <i class="fas fa-arrow-left"></i> Back to Roadmaps
        </a>
        
        <div class="glass p-6 sm:p-8 rounded-3xl relative overflow-hidden">
            <div class="absolute -right-20 -top-20 w-64 h-64 bg-{{ $roadmap['color'] }}-500/10 rounded-full blur-3xl"></div>
            
            <div class="flex flex-col sm:flex-row gap-6 items-start sm:items-center relative z-10">
                <div class="w-16 h-16 sm:w-20 sm:h-20 shrink-0 rounded-2xl bg-{{ $roadmap['color'] }}-500/20 border border-{{ $roadmap['color'] }}-500/30 flex items-center justify-center text-{{ $roadmap['color'] }}-400 text-3xl shadow-lg shadow-{{ $roadmap['color'] }}-500/20">
                    <i class="{{ $roadmap['icon'] }}"></i>
                </div>
                
                <div class="flex-1">
                    <h1 class="text-2xl sm:text-3xl font-bold text-white mb-2">{{ $roadmap['title'] }} Roadmap</h1>
                    <p class="text-slate-400 text-sm sm:text-base leading-relaxed">{{ $roadmap['description'] }}</p>
                </div>
            </div>
            
            <div class="grid grid-cols-2 sm:flex sm:flex-row gap-4 mt-8 pt-6 border-t border-slate-800 relative z-10">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-slate-800 flex items-center justify-center text-slate-400">
                        <i class="fas fa-clock text-sm"></i>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase tracking-wider text-slate-500 font-bold">Estimated Time</p>
                        <p class="text-sm font-semibold text-white">{{ $roadmap['estimated_time'] }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 sm:ml-8">
                    <div class="w-8 h-8 rounded-lg bg-slate-800 flex items-center justify-center text-slate-400">
                        <i class="fas fa-layer-group text-sm"></i>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase tracking-wider text-slate-500 font-bold">Difficulty</p>
                        <p class="text-sm font-semibold text-white">{{ $roadmap['difficulty'] }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 sm:ml-8 col-span-2">
                    <div class="w-8 h-8 rounded-lg bg-slate-800 flex items-center justify-center text-slate-400">
                        <i class="fas fa-list-ol text-sm"></i>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase tracking-wider text-slate-500 font-bold">Total Milestones</p>
                        <p class="text-sm font-semibold text-white">{{ count($roadmap['steps']) }} Steps</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Timeline -->
    <div class="relative pl-4 sm:pl-0">
        <!-- Main Line -->
        <div class="absolute left-[23px] sm:left-1/2 sm:-ml-px top-0 bottom-0 w-0.5 bg-gradient-to-b from-{{ $roadmap['color'] }}-500 via-slate-700 to-transparent"></div>

        <div class="space-y-12">
            @foreach($roadmap['steps'] as $index => $step)
            <div class="relative flex flex-col sm:flex-row items-start sm:items-center justify-between w-full group" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                
                <!-- Timeline Dot -->
                <div class="absolute left-[15px] sm:left-1/2 sm:-ml-[9px] w-[18px] h-[18px] rounded-full bg-[#0f172a] border-4 border-slate-700 group-hover:border-{{ $roadmap['color'] }}-500 transition-colors z-10 mt-5 sm:mt-0"></div>

                <!-- Content (Alternating Sides on Desktop) -->
                <div class="w-full sm:w-[calc(50%-40px)] pl-14 sm:pl-0 {{ $index % 2 == 0 ? 'sm:text-right sm:pr-10' : 'sm:ml-auto sm:pl-10' }}">
                    <div class="glass-dark p-6 rounded-2xl border border-slate-700/50 hover:border-{{ $roadmap['color'] }}-500/30 transition-all duration-300 hover:shadow-lg hover:shadow-{{ $roadmap['color'] }}-500/5">
                        
                        <div class="flex items-center gap-3 mb-3 {{ $index % 2 == 0 ? 'sm:flex-row-reverse' : '' }}">
                            <span class="flex items-center justify-center w-6 h-6 rounded-md bg-{{ $roadmap['color'] }}-500/20 text-{{ $roadmap['color'] }}-400 text-xs font-bold">
                                {{ $index + 1 }}
                            </span>
                            <span class="text-xs font-bold uppercase tracking-widest text-slate-500">{{ $step['duration'] }}</span>
                        </div>
                        
                        <h3 class="text-lg font-bold text-white mb-2">{{ $step['title'] }}</h3>
                        <p class="text-slate-400 text-sm leading-relaxed mb-4">{{ $step['description'] }}</p>
                        
                        <div class="flex flex-wrap gap-2 {{ $index % 2 == 0 ? 'sm:justify-end' : '' }}">
                            @foreach($step['skills'] as $skill)
                                <span class="px-2.5 py-1 rounded-md bg-slate-800 text-slate-300 text-[11px] font-medium border border-slate-700">
                                    {{ $skill }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>

            </div>
            @endforeach
        </div>
        
        <!-- End of timeline marker -->
        <div class="relative flex justify-start sm:justify-center mt-12" data-aos="fade-up">
            <div class="ml-[11px] sm:ml-0 flex flex-col items-center gap-3">
                <div class="w-6 h-6 rounded-full bg-{{ $roadmap['color'] }}-500 flex items-center justify-center text-white shadow-lg shadow-{{ $roadmap['color'] }}-500/40 z-10">
                    <i class="fas fa-check text-xs"></i>
                </div>
                <span class="text-xs font-bold uppercase tracking-widest text-{{ $roadmap['color'] }}-400">Job Ready</span>
            </div>
        </div>

    </div>
</div>

<div class="hidden">
    <!-- Safelist for Tailwind JIT -->
    <div class="bg-blue-500 bg-indigo-500 bg-purple-500 bg-emerald-500"></div>
    <div class="bg-blue-500/10 bg-indigo-500/10 bg-purple-500/10 bg-emerald-500/10"></div>
    <div class="bg-blue-500/20 bg-indigo-500/20 bg-purple-500/20 bg-emerald-500/20"></div>
    <div class="text-blue-400 text-indigo-400 text-purple-400 text-emerald-400"></div>
    <div class="border-blue-500/30 border-indigo-500/30 border-purple-500/30 border-emerald-500/30"></div>
    <div class="hover:bg-blue-600 hover:bg-indigo-600 hover:bg-purple-600 hover:bg-emerald-600"></div>
    <div class="shadow-blue-500/10 shadow-indigo-500/10 shadow-purple-500/10 shadow-emerald-500/10"></div>
    <div class="shadow-blue-500/20 shadow-indigo-500/20 shadow-purple-500/20 shadow-emerald-500/20"></div>
    <div class="shadow-blue-500/40 shadow-indigo-500/40 shadow-purple-500/40 shadow-emerald-500/40"></div>
    <div class="from-blue-500 from-indigo-500 from-purple-500 from-emerald-500"></div>
    <div class="group-hover:border-blue-500 group-hover:border-indigo-500 group-hover:border-purple-500 group-hover:border-emerald-500"></div>
    <div class="hover:border-blue-500/30 hover:border-indigo-500/30 hover:border-purple-500/30 hover:border-emerald-500/30"></div>
    <div class="hover:shadow-blue-500/5 hover:shadow-indigo-500/5 hover:shadow-purple-500/5 hover:shadow-emerald-500/5"></div>
</div>
@endsection
