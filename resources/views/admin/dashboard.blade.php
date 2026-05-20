@extends('layouts.app')

@section('title', 'Dashboard Admin')

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
            <p class="text-xs text-slate-500 uppercase font-bold tracking-widest">Total Pengguna</p>
            <h3 class="text-2xl font-bold text-white">{{ $stats['total_users'] }}</h3>
        </div>

        <div class="glass-dark p-6" data-aos="fade-up" data-aos-delay="100">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-500/10 rounded-xl flex items-center justify-center text-purple-500">
                    <i class="fas fa-briefcase"></i>
                </div>
                <span class="text-xs font-bold text-emerald-500">+5% <i class="fas fa-arrow-up"></i></span>
            </div>
            <p class="text-xs text-slate-500 uppercase font-bold tracking-widest">Lowongan Kerja</p>
            <h3 class="text-2xl font-bold text-white">{{ $stats['total_jobs'] }}</h3>
        </div>

        <div class="glass-dark p-6" data-aos="fade-up" data-aos-delay="200">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-amber-500/10 rounded-xl flex items-center justify-center text-amber-500">
                    <i class="fas fa-wand-magic-sparkles"></i>
                </div>
                <span class="text-xs font-bold text-emerald-500">+24% <i class="fas fa-arrow-up"></i></span>
            </div>
            <p class="text-xs text-slate-500 uppercase font-bold tracking-widest">Total Rekomendasi</p>
            <h3 class="text-2xl font-bold text-white">{{ $stats['total_recommendations'] }}</h3>
        </div>

        <div class="glass-dark p-6" data-aos="fade-up" data-aos-delay="300">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-rose-500/10 rounded-xl flex items-center justify-center text-rose-500">
                    <i class="fas fa-file-alt"></i>
                </div>
                <span class="text-xs font-bold text-rose-500">-2% <i class="fas fa-arrow-down"></i></span>
            </div>
            <p class="text-xs text-slate-500 uppercase font-bold tracking-widest">Lamaran</p>
            <h3 class="text-2xl font-bold text-white">{{ $stats['total_applications'] }}</h3>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- User Growth Chart -->
        <div class="glass-dark p-8" data-aos="fade-right">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-xl font-bold text-white">Pertumbuhan Registrasi Pengguna</h3>
                <select class="bg-slate-800 border-none rounded-lg text-xs text-slate-400">
                    <option>6 Bulan Terakhir</option>
                    <option>1 Tahun Terakhir</option>
                </select>
            </div>
            <canvas id="growthChart" height="250"></canvas>
        </div>

        <!-- Category Distribution -->
        <div class="glass-dark p-8" data-aos="fade-left">
            <h3 class="text-xl font-bold text-white mb-8">Popularitas Kategori Lowongan</h3>
            <canvas id="categoryChart" height="250"></canvas>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Popular Jobs -->
        <div class="lg:col-span-2 glass-dark p-8" data-aos="fade-right">
            <h3 class="text-xl font-bold text-white mb-8">Lowongan Paling Populer</h3>
            <div class="space-y-4">
                @foreach($popularJobs as $pop)
                <div class="flex items-center justify-between p-4 rounded-2xl bg-slate-800/50 border border-slate-700/50">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-blue-600/10 rounded-lg flex items-center justify-center text-blue-500">
                            <i class="fas fa-star"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-white">{{ $pop->job->title }}</p>
                            <p class="text-[10px] text-slate-500 uppercase">{{ $pop->job->company_name }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-blue-500">{{ $pop->count }} Kecocokan</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Assessment Stats -->
        <div class="glass-dark p-8" data-aos="fade-left">
            <h3 class="text-xl font-bold text-white mb-8">Statistik Asesmen</h3>
            <div class="space-y-8">
                <div class="text-center">
                    <div class="text-4xl font-bold text-white mb-2">{{ $stats['completed_assessments'] }}</div>
                    <p class="text-xs text-slate-500 uppercase tracking-widest font-bold">Asesmen Selesai</p>
                </div>
                <div class="pt-8 border-t border-slate-800">
                    <p class="text-sm text-slate-400 leading-relaxed italic text-center">
                        "{{ round(($stats['completed_assessments'] / max($stats['total_users'], 1)) * 100) }}% pengguna telah melengkapi profil DNA profesional mereka."
                    </p>
                </div>
            </div>
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
                label: 'Pengguna Baru',
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
