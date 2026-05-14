@extends('layouts.app')

@section('title', 'Job Explorer')

@section('content')
<div class="space-y-8">
    <!-- Filter Section -->
    <div class="glass p-6">
        <form action="{{ route('jobs.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div class="md:col-span-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by job title, company, or keyword..." class="w-full bg-slate-900 border-slate-700 rounded-xl px-4 py-3 text-white focus:ring-blue-500">
            </div>
            <div>
                <select name="category" class="w-full bg-slate-900 border-slate-700 rounded-xl px-4 py-3 text-white focus:ring-blue-500">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="location" class="w-full bg-slate-900 border-slate-700 rounded-xl px-4 py-3 text-white focus:ring-blue-500">
                    <option value="">All Locations</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc }}" {{ request('location') == $loc ? 'selected' : '' }}>{{ $loc }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-premium">Filter Results</button>
        </form>
    </div>

    <!-- Job Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($jobs as $job)
        <div class="glass-dark p-6 card-hover flex flex-col h-full animate-fade-in" style="animation-delay: {{ $loop->index % 6 * 50 }}ms">
            <div class="flex items-start justify-between mb-6">
                <div class="w-12 h-12 bg-slate-800 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-building text-slate-500 text-xl"></i>
                </div>
                <span class="px-3 py-1 rounded-full bg-blue-500/10 text-blue-400 text-[10px] font-bold uppercase tracking-widest">{{ $job->type }}</span>
            </div>

            <div class="flex-1">
                <h3 class="text-xl font-bold text-white mb-1">{{ $job->title }}</h3>
                <p class="text-sm text-blue-500 mb-4">{{ $job->company_name }}</p>
                <p class="text-slate-400 text-sm line-clamp-2 mb-6">{{ $job->description }}</p>
            </div>

            <div class="space-y-4 pt-6 border-t border-slate-800">
                <div class="flex items-center justify-between text-xs font-medium">
                    <span class="text-slate-500"><i class="fas fa-map-marker-alt mr-1"></i> {{ $job->location }}</span>
                    <span class="text-emerald-500 font-bold">{{ $job->salary_range }}</span>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('jobs.show', $job->slug) }}" class="flex-1 py-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-center text-sm font-bold transition-all">View Details</a>
                    <form action="{{ route('tracker.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="job_id" value="{{ $job->id }}">
                        <button type="submit" class="w-11 h-11 rounded-xl bg-slate-800 hover:bg-blue-600 text-slate-400 hover:text-white transition-all flex items-center justify-center" title="Save to Tracker">
                            <i class="fas fa-bookmark"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="mt-12">
        {{ $jobs->links() }}
    </div>
</div>
@endsection
