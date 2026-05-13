@extends('layouts.app')

@section('title', 'Recommendation Results')

@section('content')
<div class="space-y-8">
    <div class="text-center max-w-2xl mx-auto mb-12" data-aos="fade-up">
        <h1 class="text-4xl font-extrabold text-white mb-4">Your Career <span class="text-gradient">Blueprints</span></h1>
        <p class="text-slate-400">Our Certainty Factor engine has analyzed your profile against thousands of data points. Here are your top career matches.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach($recommendations as $rec)
        <div class="glass-dark overflow-hidden flex flex-col card-hover border-t-4 {{ $loop->first ? 'border-blue-600 shadow-2xl shadow-blue-600/10' : 'border-slate-700' }}" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
            <div class="p-8 flex-1">
                <div class="flex items-center justify-between mb-6">
                    <span class="px-3 py-1 rounded-full bg-blue-600/10 text-blue-500 text-[10px] font-bold uppercase tracking-widest">{{ $rec->job->category->name }}</span>
                    <div class="text-right">
                        <span class="text-2xl font-bold text-white">{{ number_format($rec->confidence, 1) }}%</span>
                        <p class="text-[8px] text-slate-500 uppercase font-bold">CF Confidence</p>
                    </div>
                </div>
                
                <h3 class="text-2xl font-bold text-white mb-2">{{ $rec->job->title }}</h3>
                <p class="text-sm text-slate-500 mb-6"><i class="fas fa-building mr-2"></i> {{ $rec->job->company_name }}</p>
                
                <div class="space-y-4">
                    <div class="p-4 rounded-xl bg-slate-800/50 border border-slate-700/50">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Expert Rationale</p>
                        <p class="text-sm text-slate-300 italic">{{ $rec->explanation }}</p>
                    </div>
                </div>
            </div>
            
            <div class="p-6 bg-slate-800/30 border-t border-slate-800 flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500">{{ $rec->job->location }}</span>
                <a href="{{ route('jobs.show', $rec->job->slug) }}" class="text-sm font-bold text-blue-500 hover:text-blue-400">View Detail <i class="fas fa-arrow-right ml-1"></i></a>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-12 glass p-8 text-center" data-aos="fade-up">
        <h3 class="text-xl font-bold text-white mb-4">Not what you were looking for?</h3>
        <p class="text-slate-400 mb-6">Try refining your skill levels or selecting more interests for more accurate results.</p>
        <a href="{{ route('expert.index') }}" class="btn-premium px-10">Retake Assessment</a>
    </div>
</div>
@endsection
