@extends('layouts.app')

@section('title', 'Salary Insights')

@section('content')
<div class="space-y-8">
    {{-- Header --}}
    <div class="animate-fade-in">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-400 text-xs font-bold tracking-widest uppercase mb-3 border border-emerald-500/20">
            <i class="fas fa-money-bill-trend-up"></i>
            Market Intelligence
        </div>
        <h1 class="text-3xl font-extrabold text-white mb-1">Salary <span class="text-gradient">Insights</span></h1>
        <p class="text-slate-400 text-sm">Real salary data extracted from {{ $overallStats['total_jobs'] }} active job listings across {{ $overallStats['categories'] }} categories.</p>
    </div>

    {{-- Quick Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="glass-dark p-5 animate-fade-in" style="animation-delay: 0ms">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 bg-emerald-600/10 rounded-xl flex items-center justify-center">
                    <i class="fas fa-arrow-trend-up text-emerald-400 text-sm"></i>
                </div>
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Highest Avg</span>
            </div>
            <p class="text-xl sm:text-2xl font-extrabold text-white truncate">{{ $overallStats['highest_avg'] > 0 ? 'Rp ' . number_format($overallStats['highest_avg'], 0, ',', '.') : '-' }}</p>
        </div>
        <div class="glass-dark p-5 animate-fade-in" style="animation-delay: 60ms">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 bg-blue-600/10 rounded-xl flex items-center justify-center">
                    <i class="fas fa-arrow-trend-down text-blue-400 text-sm"></i>
                </div>
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Lowest Avg</span>
            </div>
            <p class="text-xl sm:text-2xl font-extrabold text-white truncate">{{ $overallStats['lowest_avg'] > 0 ? 'Rp ' . number_format($overallStats['lowest_avg'], 0, ',', '.') : '-' }}</p>
        </div>
        <div class="glass-dark p-5 animate-fade-in" style="animation-delay: 120ms">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 bg-purple-600/10 rounded-xl flex items-center justify-center">
                    <i class="fas fa-layer-group text-purple-400 text-sm"></i>
                </div>
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Categories</span>
            </div>
            <p class="text-2xl font-extrabold text-white">{{ $overallStats['categories'] }}</p>
        </div>
        <div class="glass-dark p-5 animate-fade-in" style="animation-delay: 180ms">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 bg-amber-600/10 rounded-xl flex items-center justify-center">
                    <i class="fas fa-briefcase text-amber-400 text-sm"></i>
                </div>
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Total Jobs</span>
            </div>
            <p class="text-2xl font-extrabold text-white">{{ $overallStats['total_jobs'] }}</p>
        </div>
    </div>

    @if(count($salaryByCategory) > 0)
    {{-- Chart + Category Breakdown --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">

        {{-- Bar Chart --}}
        <div class="lg:col-span-3 glass-dark p-6 lg:p-8 animate-fade-in" style="animation-delay: 200ms">
            <h3 class="text-lg font-bold text-white mb-6"><i class="fas fa-chart-bar text-blue-400 mr-2"></i> Average Salary by Category</h3>
            <div class="h-80">
                <canvas id="salaryChart"></canvas>
            </div>
        </div>

        {{-- Category Cards --}}
        <div class="lg:col-span-2 space-y-3">
            @foreach($salaryByCategory as $idx => $cat)
            <div class="glass-dark p-5 animate-fade-in" style="animation-delay: {{ 250 + $idx * 60 }}ms">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 bg-{{ $cat['color'] }}-600/10 rounded-xl flex items-center justify-center">
                            <i class="fas {{ $cat['icon'] }} text-{{ $cat['color'] }}-400 text-sm"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-white">{{ $cat['category'] }}</p>
                            <p class="text-[10px] text-slate-500">{{ $cat['jobs_count'] }} jobs</p>
                        </div>
                    </div>
                    <span class="text-xs font-bold text-emerald-400">#{{ $idx + 1 }}</span>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between text-xs">
                        <span class="text-slate-500">Avg Salary</span>
                        <span class="text-white font-bold text-xs sm:text-sm truncate">Rp {{ number_format($cat['avg'], 0, ',', '.') }}</span>
                    </div>
                    <div class="w-full h-2 bg-slate-800 rounded-full overflow-hidden">
                        @php $maxAvg = $salaryByCategory[0]['avg']; @endphp
                        <div class="h-full rounded-full bg-gradient-to-r from-{{ $cat['color'] }}-500 to-{{ $cat['color'] }}-400"
                             style="width: {{ $maxAvg > 0 ? round(($cat['avg'] / $maxAvg) * 100) : 0 }}%"></div>
                    </div>
                    <div class="flex justify-between text-[10px] text-slate-600">
                        <span>Min: Rp {{ number_format($cat['min'], 0, ',', '.') }}</span>
                        <span>Max: Rp {{ number_format($cat['max'], 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Top Paying Jobs --}}
    @if($topJobs->count() > 0)
    <div class="animate-fade-in" style="animation-delay: 400ms">
        <h2 class="text-2xl font-bold text-white mb-2"><i class="fas fa-trophy text-amber-400 mr-2"></i> Top Paying Positions</h2>
        <p class="text-slate-400 text-sm mb-6">The highest-salary job listings currently available.</p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($topJobs as $idx => $job)
            <div class="glass-dark p-5 flex items-center justify-between gap-4 card-hover {{ $idx < 3 ? 'border-l-4 border-amber-500' : '' }}">
                <div class="flex items-center gap-4 min-w-0">
                    <div class="w-10 h-10 rounded-xl {{ $idx < 3 ? 'bg-gradient-to-br from-amber-500 to-orange-600' : 'bg-slate-800' }} flex items-center justify-center text-white font-extrabold text-sm shrink-0">
                        {{ $idx < 3 ? '#' . ($idx + 1) : ($idx + 1) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-bold text-white truncate">{{ $job['title'] }}</p>
                        <p class="text-[11px] text-slate-400 truncate">{{ $job['company'] }} • {{ $job['location'] }}</p>
                    </div>
                </div>
                <div class="text-right shrink-0">
                    <p class="text-sm font-bold text-emerald-400">Rp {{ number_format($job['avg'], 0, ',', '.') }}</p>
                    <a href="{{ route('jobs.show', $job['slug']) }}" class="text-[10px] text-blue-500 hover:underline">View <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @if(count($salaryByCategory) === 0)
    <div class="glass p-16 text-center">
        <div class="w-20 h-20 mx-auto bg-slate-800 rounded-3xl flex items-center justify-center mb-6">
            <i class="fas fa-chart-bar text-slate-600 text-3xl"></i>
        </div>
        <h3 class="text-xl font-bold text-white mb-3">No Salary Data Available</h3>
        <p class="text-slate-400 max-w-md mx-auto mb-6">Salary insights will appear once job listings with salary information are imported.</p>
        <a href="{{ route('jobs.index') }}" class="btn-premium px-8">Browse Jobs</a>
    </div>
    @endif
</div>

@push('scripts')
@if(count($salaryByCategory) > 0)
<script>
const ctx = document.getElementById('salaryChart').getContext('2d');
const labels = {!! json_encode(array_column($salaryByCategory, 'category')) !!};
const avgData = {!! json_encode(array_map(fn($c) => round($c['avg'] / 1000000, 1), $salaryByCategory)) !!};
const minData = {!! json_encode(array_map(fn($c) => round($c['min'] / 1000000, 1), $salaryByCategory)) !!};
const maxData = {!! json_encode(array_map(fn($c) => round($c['max'] / 1000000, 1), $salaryByCategory)) !!};

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [
            {
                label: 'Average (Juta)',
                data: avgData,
                backgroundColor: 'rgba(37, 99, 235, 0.7)',
                borderColor: 'rgba(37, 99, 235, 1)',
                borderWidth: 1,
                borderRadius: 8,
                barPercentage: 0.6,
            },
            {
                label: 'Max (Juta)',
                data: maxData,
                backgroundColor: 'rgba(16, 185, 129, 0.3)',
                borderColor: 'rgba(16, 185, 129, 0.8)',
                borderWidth: 1,
                borderRadius: 8,
                barPercentage: 0.6,
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                labels: { color: '#94a3b8', font: { size: 11, weight: '600' } }
            },
            tooltip: {
                callbacks: {
                    label: function(ctx) {
                        return ctx.dataset.label + ': Rp ' + ctx.parsed.y + ' Juta';
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    color: '#64748b',
                    callback: function(v) { return 'Rp ' + v + ' Jt'; }
                },
                grid: { color: 'rgba(255,255,255,0.05)' }
            },
            x: {
                ticks: { color: '#94a3b8', font: { size: 10 } },
                grid: { display: false }
            }
        }
    }
});
</script>
@endif
@endpush
@endsection
