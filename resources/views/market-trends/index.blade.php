@extends('layouts.app')

@section('title', 'Tren Pasar Kerja')

@section('content')
<div class="space-y-8">

    {{-- ── Header ─────────────────────────────────────────────────────────── --}}
    <div data-aos="fade-up">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/10 text-indigo-400 text-xs font-bold tracking-widest uppercase mb-3 border border-indigo-500/20">
            <i class="fas fa-chart-mixed"></i>
            Analisis Real-time
        </div>
        <h1 class="text-3xl font-extrabold text-white mb-1">
            Tren <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-blue-400">Pasar Kerja</span>
        </h1>
        <p class="text-slate-400 text-sm max-w-2xl">
            Analisis mendalam dari <span class="text-white font-semibold">{{ number_format($totalJobs) }} lowongan aktif</span> — temukan tren, skill yang dicari, dan peluang terbaik di pasar kerja saat ini.
        </p>
    </div>

    {{-- ── Section 1: Hero Stats ────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4" data-aos="fade-up" data-aos-delay="50">
        <div class="glass-dark p-5 border-t-4 border-indigo-500">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 bg-indigo-500/10 rounded-xl flex items-center justify-center">
                    <i class="fas fa-briefcase text-indigo-400 text-sm"></i>
                </div>
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Total Lowongan</span>
            </div>
            <p class="text-3xl font-extrabold text-white">{{ number_format($heroStats['total_jobs']) }}</p>
            <p class="text-xs text-slate-500 mt-1">Lowongan aktif saat ini</p>
        </div>

        <div class="glass-dark p-5 border-t-4 border-blue-500">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 bg-blue-500/10 rounded-xl flex items-center justify-center">
                    <i class="fas fa-layer-group text-blue-400 text-sm"></i>
                </div>
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Kategori</span>
            </div>
            <p class="text-3xl font-extrabold text-white">{{ $heroStats['total_categories'] }}</p>
            <p class="text-xs text-slate-500 mt-1">Bidang industri tersedia</p>
        </div>

        <div class="glass-dark p-5 border-t-4 border-emerald-500">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 bg-emerald-500/10 rounded-xl flex items-center justify-center">
                    <i class="fas fa-location-dot text-emerald-400 text-sm"></i>
                </div>
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Kota Terbanyak</span>
            </div>
            <p class="text-2xl font-extrabold text-white truncate">{{ $heroStats['top_city'] }}</p>
            <p class="text-xs text-slate-500 mt-1">{{ number_format($heroStats['top_city_count']) }} lowongan</p>
        </div>

        <div class="glass-dark p-5 border-t-4 border-amber-500">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 bg-amber-500/10 rounded-xl flex items-center justify-center">
                    <i class="fas fa-fire text-amber-400 text-sm"></i>
                </div>
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Skill #1 Dicari</span>
            </div>
            <p class="text-2xl font-extrabold text-white truncate">{{ $heroStats['top_skill'] }}</p>
            <p class="text-xs text-slate-500 mt-1">Paling banyak diminta</p>
        </div>
    </div>

    {{-- ── Section 2 + 4: Distribusi Kategori & Tipe Pekerjaan ────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

        {{-- Donut Chart Kategori (col-3) --}}
        <div class="lg:col-span-3 glass-dark p-6 lg:p-8" data-aos="fade-up" data-aos-delay="100">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-lg font-bold text-white">Distribusi per Kategori</h2>
                    <p class="text-xs text-slate-500">Industri mana yang paling banyak buka lowongan</p>
                </div>
                <div class="w-9 h-9 bg-indigo-500/10 rounded-xl flex items-center justify-center">
                    <i class="fas fa-chart-pie text-indigo-400"></i>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
                <div class="relative h-56">
                    <canvas id="categoryDonut"></canvas>
                </div>
                <div class="space-y-2">
                    @foreach($categoryDist as $cat)
                    <div class="flex items-center justify-between gap-3 group">
                        <div class="flex items-center gap-2 min-w-0">
                            <div class="w-2.5 h-2.5 rounded-full shrink-0" style="background: {{ $cat['color'] }}"></div>
                            <span class="text-xs text-slate-300 truncate">{{ $cat['name'] }}</span>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <span class="text-xs font-bold text-white">{{ $cat['count'] }}</span>
                            <span class="text-[10px] text-slate-500 w-8 text-right">{{ $cat['percent'] }}%</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Tipe Pekerjaan (col-2) --}}
        <div class="lg:col-span-2 glass-dark p-6 lg:p-8" data-aos="fade-up" data-aos-delay="150">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-lg font-bold text-white">Tipe Pekerjaan</h2>
                    <p class="text-xs text-slate-500">Proporsi Remote, Onsite, dll</p>
                </div>
                <div class="w-9 h-9 bg-purple-500/10 rounded-xl flex items-center justify-center">
                    <i class="fas fa-laptop-house text-purple-400"></i>
                </div>
            </div>
            <div class="relative h-44 mb-4">
                <canvas id="typeDonut"></canvas>
            </div>
            <div class="space-y-2 mt-4">
                @php
                    $typeColors = ['#3b82f6','#10b981','#f59e0b','#a855f7','#ef4444','#06b6d4'];
                @endphp
                @foreach($typeDist as $i => $t)
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-2.5 h-2.5 rounded-full" style="background: {{ $typeColors[$i % count($typeColors)] }}"></div>
                        <span class="text-xs text-slate-300">{{ $t['type'] }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-16 h-1.5 bg-slate-800 rounded-full overflow-hidden">
                            <div class="h-full rounded-full" style="width:{{ $t['percent'] }}%; background: {{ $typeColors[$i % count($typeColors)] }}"></div>
                        </div>
                        <span class="text-xs font-bold text-white w-8 text-right">{{ $t['percent'] }}%</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ── Section 3: Top Skills ────────────────────────────────────────────── --}}
    <div class="glass-dark p-6 lg:p-8" data-aos="fade-up" data-aos-delay="100">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-lg font-bold text-white">Top Skill yang Paling Dicari</h2>
                <p class="text-xs text-slate-500">Diekstrak dari {{ number_format($totalJobs) }} deskripsi & persyaratan lowongan</p>
            </div>
            <div class="w-9 h-9 bg-amber-500/10 rounded-xl flex items-center justify-center">
                <i class="fas fa-fire text-amber-400"></i>
            </div>
        </div>
        @if(count($topSkills) > 0)
        @php $maxSkillCount = max($topSkills); @endphp
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-3">
            @foreach($topSkills as $skill => $count)
            @php
                $percent = $maxSkillCount > 0 ? round(($count / $maxSkillCount) * 100) : 0;
                $rank = $loop->iteration;
                $colors = ['#f59e0b','#3b82f6','#10b981','#a855f7','#ef4444','#06b6d4','#f97316','#6366f1','#14b8a6','#ec4899','#8b5cf6','#84cc16','#0ea5e9','#d946ef','#f43f5e'];
                $color = $colors[($rank - 1) % count($colors)];
            @endphp
            <div class="flex items-center gap-3 group">
                <span class="text-[10px] font-black text-slate-600 w-5 text-right shrink-0">#{{ $rank }}</span>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs font-semibold text-slate-200 truncate">{{ $skill }}</span>
                        <span class="text-[10px] text-slate-500 ml-2 shrink-0">{{ $count }} lowongan</span>
                    </div>
                    <div class="h-1.5 bg-slate-800 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-700" style="width: {{ $percent }}%; background: {{ $color }}"></div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-8 text-slate-600"><i class="fas fa-chart-bar text-2xl mb-2"></i><p class="text-sm">Belum ada data skill.</p></div>
        @endif
    </div>

    {{-- ── Section 5 + 6: Top Cities + Monthly Trend ───────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Top Cities --}}
        <div class="glass-dark p-6 lg:p-8" data-aos="fade-up" data-aos-delay="100">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-lg font-bold text-white">Top 10 Kota</h2>
                    <p class="text-xs text-slate-500">Kota dengan paling banyak lowongan aktif</p>
                </div>
                <div class="w-9 h-9 bg-emerald-500/10 rounded-xl flex items-center justify-center">
                    <i class="fas fa-map-location-dot text-emerald-400"></i>
                </div>
            </div>
            <div class="space-y-3">
                @foreach($topCities as $city => $count)
                @php
                    $pct = $maxCityCount > 0 ? round(($count / $maxCityCount) * 100) : 0;
                    $medalColors = ['text-amber-400','text-slate-400','text-orange-600'];
                @endphp
                <div class="flex items-center gap-3">
                    <span class="text-sm font-black {{ $medalColors[$loop->index] ?? 'text-slate-600' }} w-5 text-right shrink-0">
                        @if($loop->index < 3)
                            <i class="fas fa-medal"></i>
                        @else
                            {{ $loop->iteration }}
                        @endif
                    </span>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-semibold text-slate-200 truncate">{{ $city }}</span>
                            <span class="text-[10px] text-slate-500 ml-2 shrink-0">{{ $count }} lowongan</span>
                        </div>
                        <div class="h-1.5 bg-slate-800 rounded-full overflow-hidden">
                            <div class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-teal-400 transition-all duration-700" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                </div>
                @endforeach
                @if(empty($topCities))
                <p class="text-sm text-slate-500 text-center py-4">Belum ada data lokasi.</p>
                @endif
            </div>
        </div>

        {{-- Monthly Trend Line Chart --}}
        <div class="glass-dark p-6 lg:p-8" data-aos="fade-up" data-aos-delay="150">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-lg font-bold text-white">Tren Lowongan (6 Bulan)</h2>
                    <p class="text-xs text-slate-500">Pertumbuhan jumlah lowongan baru per bulan</p>
                </div>
                <div class="w-9 h-9 bg-blue-500/10 rounded-xl flex items-center justify-center">
                    <i class="fas fa-chart-line text-blue-400"></i>
                </div>
            </div>
            <div class="h-56">
                <canvas id="trendLine"></canvas>
            </div>
        </div>
    </div>

    {{-- ── Section 7: Top Hiring Companies ────────────────────────────────── --}}
    @if(count($topCompanies) > 0)
    <div class="glass-dark p-6 lg:p-8" data-aos="fade-up" data-aos-delay="100">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-lg font-bold text-white">Perusahaan Paling Aktif Rekrut</h2>
                <p class="text-xs text-slate-500">Top perusahaan berdasarkan jumlah lowongan aktif</p>
            </div>
            <div class="w-9 h-9 bg-rose-500/10 rounded-xl flex items-center justify-center">
                <i class="fas fa-building text-rose-400"></i>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($topCompanies as $company => $count)
            @php
                $gradients = [
                    'from-blue-600/20 to-indigo-600/20 border-blue-500/20',
                    'from-emerald-600/20 to-teal-600/20 border-emerald-500/20',
                    'from-purple-600/20 to-pink-600/20 border-purple-500/20',
                    'from-amber-600/20 to-orange-600/20 border-amber-500/20',
                    'from-rose-600/20 to-red-600/20 border-rose-500/20',
                    'from-cyan-600/20 to-sky-600/20 border-cyan-500/20',
                    'from-indigo-600/20 to-violet-600/20 border-indigo-500/20',
                    'from-teal-600/20 to-emerald-600/20 border-teal-500/20',
                    'from-pink-600/20 to-rose-600/20 border-pink-500/20',
                ];
                $grad = $gradients[$loop->index % count($gradients)];
                $initials = collect(explode(' ', $company))->take(2)->map(fn($w) => strtoupper(substr($w, 0, 1)))->implode('');
            @endphp
            <div class="bg-gradient-to-br {{ $grad }} border rounded-2xl p-4 flex items-center gap-4 hover:-translate-y-1 transition-transform duration-300">
                <div class="w-12 h-12 rounded-xl bg-slate-800 flex items-center justify-center text-white font-extrabold text-sm shrink-0">
                    {{ $initials }}
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-bold text-white truncate">{{ $company }}</p>
                    <p class="text-xs text-slate-400">{{ $count }} lowongan aktif</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── Section 8: Salary vs Demand Matrix ─────────────────────────────── --}}
    @if(count($salaryDemand) > 0)
    <div class="glass-dark p-6 lg:p-8" data-aos="fade-up" data-aos-delay="100">
        <div class="flex items-center justify-between mb-2">
            <div>
                <h2 class="text-lg font-bold text-white">Salary vs Demand Matrix</h2>
                <p class="text-xs text-slate-500 mb-6">Temukan sweet spot: kategori bergaji tinggi <span class="text-emerald-400">sekaligus</span> banyak lowongan</p>
            </div>
            <div class="w-9 h-9 bg-emerald-500/10 rounded-xl flex items-center justify-center">
                <i class="fas fa-crosshairs text-emerald-400"></i>
            </div>
        </div>
        <div class="relative h-72">
            <canvas id="scatterMatrix"></canvas>
        </div>
        <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-3">
            <div class="p-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-center">
                <p class="text-[10px] text-emerald-400 font-bold uppercase tracking-widest">Sweet Spot</p>
                <p class="text-xs text-slate-400 mt-1">Gaji tinggi + banyak lowongan</p>
            </div>
            <div class="p-3 rounded-xl bg-blue-500/10 border border-blue-500/20 text-center">
                <p class="text-[10px] text-blue-400 font-bold uppercase tracking-widest">Premium</p>
                <p class="text-xs text-slate-400 mt-1">Gaji tinggi, lowongan terbatas</p>
            </div>
            <div class="p-3 rounded-xl bg-amber-500/10 border border-amber-500/20 text-center">
                <p class="text-[10px] text-amber-400 font-bold uppercase tracking-widest">Volume</p>
                <p class="text-xs text-slate-400 mt-1">Banyak lowongan, gaji menengah</p>
            </div>
            <div class="p-3 rounded-xl bg-slate-700/50 border border-slate-600/50 text-center">
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Kompetitif</p>
                <p class="text-xs text-slate-500 mt-1">Gaji dan persaingan standar</p>
            </div>
        </div>
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
// ── Palette Helper ─────────────────────────────────────────────────────────
const palette = ['#3b82f6','#10b981','#f59e0b','#a855f7','#ef4444','#06b6d4','#f97316','#6366f1','#14b8a6','#ec4899'];

// ── Section 2: Category Donut ──────────────────────────────────────────────
const catData  = {!! json_encode(array_column($categoryDist, 'count')) !!};
const catLabels = {!! json_encode(array_column($categoryDist, 'name')) !!};
const catColors = {!! json_encode(array_column($categoryDist, 'color')) !!};

new Chart(document.getElementById('categoryDonut'), {
    type: 'doughnut',
    data: {
        labels: catLabels,
        datasets: [{ data: catData, backgroundColor: catColors, borderColor: 'transparent', borderWidth: 0, hoverOffset: 8 }]
    },
    options: {
        responsive: true, maintainAspectRatio: false, cutout: '65%',
        plugins: {
            legend: { display: false },
            tooltip: { callbacks: { label: ctx => ` ${ctx.label}: ${ctx.parsed} lowongan` } }
        }
    }
});

// ── Section 4: Type Donut ──────────────────────────────────────────────────
const typeData   = {!! json_encode(array_column($typeDist, 'count')) !!};
const typeLabels = {!! json_encode(array_column($typeDist, 'type')) !!};

new Chart(document.getElementById('typeDonut'), {
    type: 'doughnut',
    data: {
        labels: typeLabels,
        datasets: [{ data: typeData, backgroundColor: palette, borderColor: 'transparent', borderWidth: 0, hoverOffset: 6 }]
    },
    options: {
        responsive: true, maintainAspectRatio: false, cutout: '60%',
        plugins: {
            legend: { display: false },
            tooltip: { callbacks: { label: ctx => ` ${ctx.label}: ${ctx.parsed}` } }
        }
    }
});

// ── Section 6: Monthly Trend Line ─────────────────────────────────────────
const trendLabels = {!! json_encode(array_column($monthlyTrend, 'label')) !!};
const trendCounts = {!! json_encode(array_column($monthlyTrend, 'count')) !!};

new Chart(document.getElementById('trendLine'), {
    type: 'line',
    data: {
        labels: trendLabels,
        datasets: [{
            label: 'Lowongan Baru',
            data: trendCounts,
            borderColor: '#3b82f6',
            backgroundColor: 'rgba(59,130,246,0.12)',
            borderWidth: 2.5,
            pointBackgroundColor: '#3b82f6',
            pointRadius: 4,
            pointHoverRadius: 6,
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { color: '#64748b', font: { size: 11 } },
                grid: { color: 'rgba(255,255,255,0.05)' }
            },
            x: {
                ticks: { color: '#94a3b8', font: { size: 10 } },
                grid: { display: false }
            }
        }
    }
});

// ── Section 8: Scatter Matrix ─────────────────────────────────────────────
@if(count($salaryDemand) > 0)
const scatterData = {!! json_encode($salaryDemand) !!};
const scatterPoints = scatterData.map(d => ({ x: d.demand, y: d.avg_salary, label: d.category, color: d.color }));

new Chart(document.getElementById('scatterMatrix'), {
    type: 'scatter',
    data: {
        datasets: scatterPoints.map(pt => ({
            label: pt.label,
            data: [{ x: pt.x, y: pt.y }],
            backgroundColor: pt.color + 'cc',
            borderColor: pt.color,
            borderWidth: 2,
            pointRadius: 10,
            pointHoverRadius: 14
        }))
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx => {
                        const d = ctx.dataset.label;
                        return [`📌 ${d}`, `💰 Rp ${ctx.parsed.y} Juta/bln`, `📋 ${ctx.parsed.x} lowongan`];
                    }
                }
            }
        },
        scales: {
            x: {
                title: { display: true, text: 'Jumlah Lowongan', color: '#64748b', font: { size: 11 } },
                ticks: { color: '#64748b', font: { size: 10 } },
                grid: { color: 'rgba(255,255,255,0.05)' }
            },
            y: {
                title: { display: true, text: 'Rata-rata Gaji (Juta)', color: '#64748b', font: { size: 11 } },
                ticks: { color: '#64748b', font: { size: 10 }, callback: v => 'Rp ' + v + ' Jt' },
                grid: { color: 'rgba(255,255,255,0.05)' }
            }
        }
    }
});
@endif
</script>
@endpush
