@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="space-y-8">
    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="glass-dark p-6" data-aos="fade-up">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-500/10 rounded-xl flex items-center justify-center text-blue-500">
                    <i class="fas fa-users"></i>
                </div>
                <span class="text-xs font-bold text-emerald-500">+12% <i class="fas fa-arrow-up"></i></span>
            </div>
            <p class="text-xs text-slate-500 uppercase font-bold tracking-widest">Total Users</p>
            <h3 class="text-2xl font-bold text-white">{{ $stats['total_users'] }}</h3>
        </div>

        <div class="glass-dark p-6" data-aos="fade-up" data-aos-delay="100">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-500/10 rounded-xl flex items-center justify-center text-purple-500">
                    <i class="fas fa-briefcase"></i>
                </div>
                <span class="text-xs font-bold text-emerald-500">+5% <i class="fas fa-arrow-up"></i></span>
            </div>
            <p class="text-xs text-slate-500 uppercase font-bold tracking-widest">Job Vacancies</p>
            <h3 class="text-2xl font-bold text-white">{{ $stats['total_jobs'] }}</h3>
        </div>

        <div class="glass-dark p-6" data-aos="fade-up" data-aos-delay="200">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-amber-500/10 rounded-xl flex items-center justify-center text-amber-500">
                    <i class="fas fa-wand-magic-sparkles"></i>
                </div>
                <span class="text-xs font-bold text-emerald-500">+24% <i class="fas fa-arrow-up"></i></span>
            </div>
            <p class="text-xs text-slate-500 uppercase font-bold tracking-widest">Total Recs</p>
            <h3 class="text-2xl font-bold text-white">{{ $stats['total_recommendations'] }}</h3>
        </div>

        <div class="glass-dark p-6" data-aos="fade-up" data-aos-delay="300">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-rose-500/10 rounded-xl flex items-center justify-center text-rose-500">
                    <i class="fas fa-file-alt"></i>
                </div>
                <span class="text-xs font-bold text-rose-500">-2% <i class="fas fa-arrow-down"></i></span>
            </div>
            <p class="text-xs text-slate-500 uppercase font-bold tracking-widest">Applications</p>
            <h3 class="text-2xl font-bold text-white">{{ $stats['total_applications'] }}</h3>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- User Growth Chart -->
        <div class="glass-dark p-8" data-aos="fade-right">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-xl font-bold text-white">User Registration Growth</h3>
                <select class="bg-slate-800 border-none rounded-lg text-xs text-slate-400">
                    <option>Last 6 Months</option>
                    <option>Last Year</option>
                </select>
            </div>
            <canvas id="growthChart" height="250"></canvas>
        </div>

        <!-- Category Distribution -->
        <div class="glass-dark p-8" data-aos="fade-left">
            <h3 class="text-xl font-bold text-white mb-8">Job Categories Popularity</h3>
            <canvas id="categoryChart" height="250"></canvas>
        </div>
    </div>

    <!-- Recent System Activity -->
    <div class="glass-dark p-8" data-aos="fade-up">
        <h3 class="text-xl font-bold text-white mb-8">Recent System Activity</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-slate-800">
                        <th class="pb-4 text-xs font-bold uppercase tracking-widest text-slate-500">User</th>
                        <th class="pb-4 text-xs font-bold uppercase tracking-widest text-slate-500">Action</th>
                        <th class="pb-4 text-xs font-bold uppercase tracking-widest text-slate-500">Status</th>
                        <th class="pb-4 text-xs font-bold uppercase tracking-widest text-slate-500">Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/50">
                    @foreach(range(1, 5) as $i)
                    <tr>
                        <td class="py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-slate-700"></div>
                                <span class="text-sm font-medium text-white">User {{ $i }}</span>
                            </div>
                        </td>
                        <td class="py-4 text-sm text-slate-400">Completed Career Assessment</td>
                        <td class="py-4">
                            <span class="px-2 py-1 rounded-md bg-emerald-500/10 text-emerald-500 text-[10px] font-bold uppercase">Success</span>
                        </td>
                        <td class="py-4 text-sm text-slate-500">{{ $i }} hours ago</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Growth Chart
    const growthCtx = document.getElementById('growthChart').getContext('2d');
    new Chart(growthCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($userGrowth->pluck('month')) !!},
            datasets: [{
                label: 'New Users',
                data: {!! json_encode($userGrowth->pluck('count')) !!},
                borderColor: '#2563eb',
                backgroundColor: 'rgba(37, 99, 235, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { grid: { color: 'rgba(255, 255, 255, 0.05)' }, ticks: { color: '#64748b' } },
                x: { grid: { display: false }, ticks: { color: '#64748b' } }
            }
        }
    });

    // Category Chart
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    new Chart(categoryCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($categoryPopularity->pluck('name')) !!},
            datasets: [{
                data: {!! json_encode($categoryPopularity->pluck('count')) !!},
                backgroundColor: [
                    '#2563eb', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899', '#06b6d4'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'right', labels: { color: '#94a3b8', boxWidth: 12, padding: 20 } }
            }
        }
    });
</script>
@endpush
@endsection
