@extends('layouts.app')

@section('title', 'User Dashboard')

@section('content')
<div class="space-y-8">
    <!-- Welcome Card -->
    <div class="relative overflow-hidden glass p-8 rounded-3xl" data-aos="fade-up">
        <div class="relative z-10">
            <h1 class="text-3xl font-bold text-white mb-2">Welcome back, {{ Auth::user()->name }}! 👋</h1>
            <p class="text-slate-400 max-w-xl">Your career journey is looking promising. You have {{ $stats['recommendations'] }} new job recommendations based on your recent assessment.</p>
            <div class="mt-6 flex gap-4">
                <a href="{{ route('expert.index') }}" class="btn-premium px-6 py-2">Retake Assessment</a>
                <a href="{{ route('jobs.index') }}" class="px-6 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 transition-all text-white font-semibold">View All Jobs</a>
            </div>
        </div>
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-blue-600/10 rounded-full blur-3xl"></div>
        <div class="absolute right-10 top-1/2 -translate-y-1/2 hidden lg:block opacity-20">
            <i class="fas fa-rocket text-[120px] text-blue-500 animate-float"></i>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="glass-dark p-6 card-hover" data-aos="fade-up" data-aos-delay="100">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 bg-blue-500/10 rounded-xl flex items-center justify-center">
                    <i class="fas fa-briefcase text-blue-500"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 uppercase font-bold tracking-widest">Matched Jobs</p>
                    <h3 class="text-2xl font-bold text-white">{{ $stats['recommendations'] }}</h3>
                </div>
            </div>
            <div class="w-full bg-slate-800 h-1.5 rounded-full overflow-hidden">
                <div class="bg-blue-500 h-full w-[70%]"></div>
            </div>
        </div>

        <div class="glass-dark p-6 card-hover" data-aos="fade-up" data-aos-delay="200">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 bg-emerald-500/10 rounded-xl flex items-center justify-center">
                    <i class="fas fa-file-alt text-emerald-500"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 uppercase font-bold tracking-widest">Applications</p>
                    <h3 class="text-2xl font-bold text-white">{{ $stats['applications'] }}</h3>
                </div>
            </div>
            <div class="w-full bg-slate-800 h-1.5 rounded-full overflow-hidden">
                <div class="bg-emerald-500 h-full w-[45%]"></div>
            </div>
        </div>

        <div class="glass-dark p-6 card-hover" data-aos="fade-up" data-aos-delay="300">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 bg-purple-500/10 rounded-xl flex items-center justify-center">
                    <i class="fas fa-star text-purple-500"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 uppercase font-bold tracking-widest">Skills Logged</p>
                    <h3 class="text-2xl font-bold text-white">{{ $stats['skills'] }}</h3>
                </div>
            </div>
            <div class="w-full bg-slate-800 h-1.5 rounded-full overflow-hidden">
                <div class="bg-purple-500 h-full w-[85%]"></div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Recommendations -->
        <div class="glass-dark p-8" data-aos="fade-right">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-xl font-bold text-white">Top Recommendations</h3>
                <a href="{{ route('expert.results') }}" class="text-blue-500 text-sm hover:underline">View History</a>
            </div>
            <div class="space-y-4">
                @forelse($recentRecommendations as $rec)
                <div class="p-4 rounded-2xl bg-slate-800/50 border border-slate-700/50 flex items-center justify-between hover:bg-slate-800 transition-all">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-slate-700 rounded-lg flex items-center justify-center">
                            <i class="fas fa-building text-slate-500"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-white">{{ $rec->job->title }}</p>
                            <p class="text-xs text-slate-500">{{ $rec->job->company_name }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-blue-500">{{ number_format($rec->confidence, 0) }}% Match</p>
                        <p class="text-[10px] text-slate-600 uppercase tracking-widest font-bold">Confidence</p>
                    </div>
                </div>
                @empty
                <div class="text-center py-8">
                    <p class="text-slate-500">No recommendations yet. Start the assessment!</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Career Analytics Chart -->
        <div class="glass-dark p-8" data-aos="fade-left">
            <h3 class="text-xl font-bold text-white mb-8">Skill Breakdown</h3>
            <canvas id="skillChart" height="250"></canvas>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const ctx = document.getElementById('skillChart').getContext('2d');
    new Chart(ctx, {
        type: 'radar',
        data: {
            labels: ['PHP', 'Laravel', 'React', 'Python', 'SQL', 'UI Design'],
            datasets: [{
                label: 'Proficiency Level',
                data: [4.5, 4, 3, 2, 5, 4],
                backgroundColor: 'rgba(37, 99, 235, 0.2)',
                borderColor: 'rgba(37, 99, 235, 1)',
                pointBackgroundColor: 'rgba(37, 99, 235, 1)',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: 'rgba(37, 99, 235, 1)'
            }]
        },
        options: {
            scales: {
                r: {
                    angleLines: { color: 'rgba(255, 255, 255, 0.1)' },
                    grid: { color: 'rgba(255, 255, 255, 0.1)' },
                    pointLabels: { color: '#94a3b8', font: { size: 12 } },
                    ticks: { display: false, stepSize: 1 },
                    min: 0,
                    max: 5
                }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
</script>
@endpush
@endsection
