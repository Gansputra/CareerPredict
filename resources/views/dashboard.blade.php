@extends('layouts.app')

@section('title', 'Partner Karir Anda')

@section('content')
<div class="space-y-8">
    <!-- Welcome Card & Match Summary -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 relative overflow-hidden glass p-8 sm:p-10 rounded-3xl animate-fade-in">
            <div class="relative z-10">
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-white mb-4">Selamat datang, {{ Auth::user()->name }}!</h1>
                <p class="text-slate-400 text-base sm:text-lg max-w-xl leading-relaxed">
                    "Cara terbaik memprediksi masa depan adalah menciptakannya." 
                    <br>Mari temukan jalur karir idealmu hari ini.
                </p>
                
                <div class="mt-8 flex flex-wrap gap-4">
                    @if($progress > 0)
                    <a href="{{ route('assessment.index') }}" class="btn-premium px-8 py-3">
                        <i class="fas fa-wand-magic-sparkles mr-2"></i> Ulangi Asesmen
                    </a>
                    @else
                    <a href="{{ route('assessment.index') }}" class="btn-premium px-8 py-3">
                        <i class="fas fa-dna mr-2"></i> Mulai Tes DNA Karir
                    </a>
                    @endif
                    <a href="{{ route('jobs.index') }}" class="px-8 py-3 rounded-xl bg-slate-800 hover:bg-slate-700 transition-all text-white font-semibold">
                        Jelajahi Lowongan
                    </a>
                    <a href="{{ route('docs.index') }}" class="px-8 py-3 rounded-xl bg-slate-800/50 hover:bg-slate-750 hover:bg-slate-700 border border-slate-700/50 hover:border-slate-500 transition-all text-slate-300 hover:text-white font-semibold flex items-center gap-2">
                        <i class="fas fa-book-open text-white-500"></i> Baca Dokumentasi
                    </a>
                </div>
            </div>
            <div class="absolute -right-20 -top-20 w-80 h-80 bg-blue-600/10 rounded-full blur-3xl"></div>
            <div class="absolute right-12 bottom-12 hidden xl:block opacity-30">
                <i class="fas fa-briefcase text-[160px] text-blue-500 animate-float"></i>
            </div>
        </div>

        <!-- Top Match Card -->
        <div class="glass-dark p-8 border-t-4 {{ count($topMatches) > 0 ? 'border-emerald-500' : 'border-slate-700' }} shadow-2xl animate-fade-in" style="animation-delay: 100ms">
            <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-6">Karir Paling Cocok</h3>
            @if(count($topMatches) > 0)
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-white mb-1">{{ $topMatches[0]['job_title'] }}</h2>
                    <p class="text-emerald-500 font-bold text-lg">{{ $topMatches[0]['confidence'] }}% Kompatibilitas</p>
                </div>
                <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 mb-6">
                    <p class="text-sm text-slate-300 italic">"{{ $topMatches[0]['explanation'] }}"</p>
                </div>
                @php $topJob = \App\Models\JobListing::find($topMatches[0]['job_id']); @endphp
                @if($topJob)
                <a href="{{ route('jobs.show', $topJob->slug) }}" class="text-blue-500 font-bold text-sm hover:underline">
                    Lihat Persyaratan <i class="fas fa-arrow-right ml-1"></i>
                </a>
                @endif
            @else
                <div class="text-center py-8">
                    <div class="w-16 h-16 mx-auto bg-slate-800 rounded-2xl flex items-center justify-center mb-4">
                        <i class="fas fa-compass text-slate-600 text-2xl"></i>
                    </div>
                    <p class="text-slate-500 text-sm mb-4">Selesaikan Tes DNA Karir untuk melihat karir paling cocok.</p>
                    <a href="{{ route('assessment.index') }}" class="text-blue-500 text-sm font-bold hover:underline">
                        Mulai Tes <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            @endif
        </div>
    </div>

    {{-- ── Sponsored / Featured Partners Slider ──────────────────────────────── --}}
    @if($activeSponsors->count() > 0)
    <div class="relative overflow-hidden rounded-2xl animate-fade-in" style="animation-delay: 150ms;"
         x-data="{
            current: 0,
            total: {{ $activeSponsors->count() }},
            autoplay: null,
            next() { this.current = (this.current + 1) % this.total; },
            prev() { this.current = (this.current - 1 + this.total) % this.total; },
            goto(i) { this.current = i; },
            startAutoplay() { this.autoplay = setInterval(() => this.next(), 5000); },
            stopAutoplay()  { clearInterval(this.autoplay); }
         }"
         x-init="startAutoplay()"
         @mouseenter="stopAutoplay()"
         @mouseleave="startAutoplay()">

        {{-- Sponsored badge --}}
        <div class="absolute top-3 left-4 z-20">
            <span class="text-[9px] font-extrabold uppercase tracking-widest px-2.5 py-1 rounded-full bg-slate-950/85 text-white backdrop-blur-md border border-white/20 shadow-lg" style="text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);">
                Sponsored
            </span>
        </div>

        {{-- Arrows --}}
        @if($activeSponsors->count() > 1)
        <button @click="prev()" class="absolute left-3 top-1/2 -translate-y-1/2 z-25 w-9 h-9 rounded-full bg-black/30 hover:bg-black/50 backdrop-blur-sm text-white flex items-center justify-center transition-all border border-white/10 hover:scale-110">
            <i class="fas fa-chevron-left text-xs"></i>
        </button>
        <button @click="next()" class="absolute right-3 top-1/2 -translate-y-1/2 z-25 w-9 h-9 rounded-full bg-black/30 hover:bg-black/50 backdrop-blur-sm text-white flex items-center justify-center transition-all border border-white/10 hover:scale-110">
            <i class="fas fa-chevron-right text-xs"></i>
        </button>
        @endif

        {{-- Slides --}}
        <div class="relative h-36 sm:h-44 md:h-52 w-full bg-slate-950">
            @foreach($activeSponsors as $idx => $sponsor)
            <div x-show="current==={{ $idx }}" 
                 x-transition:enter="transition ease-out duration-500" 
                 x-transition:enter-start="opacity-0 translate-x-8" 
                 x-transition:enter-end="opacity-100 translate-x-0" 
                 x-transition:leave="transition ease-in duration-300" 
                 x-transition:leave-start="opacity-100 translate-x-0" 
                 x-transition:leave-end="opacity-0 -translate-x-8"
                 class="absolute inset-0 w-full h-full"
                 style="display: {{ $idx === 0 ? 'block' : 'none' }}">
                
                {{-- Banner Link --}}
                <a href="{{ $sponsor->link_url }}" target="_blank" class="block w-full h-full relative group">
                    {{-- Banner Image --}}
                    <img src="{{ asset('storage/' . $sponsor->image_path) }}" alt="{{ $sponsor->title }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-[1.01]">
                    
                    {{-- Soft Overlay --}}
                    <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/30 to-transparent opacity-85 group-hover:opacity-75 transition-opacity"></div>
                    
                    {{-- Sponsor Info --}}
                    <div class="absolute bottom-4 left-4 sm:left-8 z-20">
                        <span class="text-[10px] uppercase tracking-widest text-white font-extrabold mb-0.5 block" style="text-shadow: 0 2px 4px rgba(0, 0, 0, 0.9);">{{ $sponsor->title }}</span>
                    </div>

                    {{-- CTA Button --}}
                    <div class="absolute bottom-3 right-4 sm:bottom-4 sm:right-8 z-20">
                        <span class="px-5 py-2.5 rounded-xl bg-blue-600 group-hover:bg-blue-500 text-white text-xs sm:text-sm font-bold transition-all shadow-lg flex items-center gap-2 border border-blue-500/30">
                            {{ $sponsor->cta_text }} <i class="fas fa-arrow-right text-[10px]"></i>
                        </span>
                    </div>
                </a>

            </div>
            @endforeach
        </div>

        {{-- Dot indicators --}}
        @if($activeSponsors->count() > 1)
        <div class="absolute bottom-3 left-1/2 -translate-x-1/2 flex items-center gap-1.5 z-20">
            @foreach($activeSponsors as $idx => $sponsor)
            <button @click="goto({{ $idx }})" class="rounded-full transition-all duration-300" :class="current === {{ $idx }} ? 'w-5 h-1.5 bg-white' : 'w-1.5 h-1.5 bg-white/30 hover:bg-white/60'"></button>
            @endforeach
        </div>
        @endif

    </div>
    @else
    {{-- Fallback Premium Banner --}}
    <div class="relative overflow-hidden rounded-2xl glass p-8 sm:p-10 flex flex-col md:flex-row items-center justify-between gap-6 border-l-4 border-blue-500 animate-fade-in" style="animation-delay: 150ms;">
        <div class="absolute inset-0 bg-gradient-to-r from-blue-600/5 to-indigo-600/5 pointer-events-none"></div>
        <div class="relative z-10 flex flex-col sm:flex-row items-center gap-4 sm:gap-6">
            <div class="w-14 h-14 rounded-2xl bg-blue-500/15 border border-blue-500/30 flex items-center justify-center shrink-0 shadow-lg shadow-blue-500/5">
                <i class="fas fa-dna text-blue-400 text-2xl animate-pulse"></i>
            </div>
            <div class="text-center sm:text-left">
                <span class="text-[9px] uppercase tracking-widest text-blue-400 font-black mb-1 block">Fitur Unggulan • CareerPredict</span>
                <h3 class="text-lg sm:text-xl font-bold text-white mb-1">Rekomendasi Karir Berbasis Certainty Factor & AI</h3>
                <p class="text-slate-400 text-xs sm:text-sm max-w-xl">Uji kompatibilitas minat Anda dan analisis CV Anda dengan kecerdasan buatan untuk hasil yang sangat presisi.</p>
            </div>
        </div>
        <a href="{{ route('assessment.index') }}" class="shrink-0 btn-premium px-6 py-3 text-sm font-bold shadow-lg shadow-blue-600/20 whitespace-nowrap z-10">Mulai Asesmen Sekarang <i class="fas fa-arrow-right ml-1 text-xs"></i></a>
    </div>
    @endif
    {{-- ── End Ads Slider ──────────────────────────────────────────────────────── --}}

    <!-- Analytics Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Skill Radar Chart -->
        <div class="glass-dark p-8 animate-fade-in" style="animation-delay: 200ms">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="text-xl font-bold text-white">Insight Kepribadian</h3>
                    <p class="text-xs text-slate-500 mt-1">Berdasarkan skor asesmen terbaru Anda</p>
                </div>
                <div class="w-10 h-10 bg-purple-500/10 rounded-xl flex items-center justify-center text-purple-500">
                    <i class="fas fa-chart-pie"></i>
                </div>
            </div>
            @if($personalityScores->count() > 0)
            <div class="relative w-full h-[360px] sm:h-[400px] md:h-[440px] mx-auto">
                <canvas id="personalityRadar"></canvas>
            </div>
            @else
            <div class="text-center py-12">
                <div class="w-16 h-16 mx-auto bg-slate-800 rounded-2xl flex items-center justify-center mb-4">
                    <i class="fas fa-chart-bar text-slate-600 text-2xl"></i>
                </div>
                <p class="text-slate-500 text-sm">Belum ada data kepribadian. Ikuti Tes DNA Karir untuk melihat grafik Anda.</p>
            </div>
            @endif
        </div>

        <!-- Progress & Suggestions -->
        <div class="space-y-8">
            <!-- Assessment Progress -->
            <div class="glass-dark p-8 animate-fade-in" style="animation-delay: 300ms">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-white">Kekuatan Profil</h3>
                    <span class="text-blue-500 font-bold text-sm">{{ round($progress) }}%</span>
                </div>
                <div class="w-full bg-slate-800 h-3 rounded-full overflow-hidden mb-6">
                    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 h-full transition-all duration-1000" style="width: {{ $progress }}%"></div>
                </div>
                <p class="text-xs text-slate-500 leading-relaxed">
                    @if($progress == 0)
                        Mulai Tes DNA Karir untuk membangun profil dan membuka insight berbasis AI.
                    @elseif($progress < 100)
                        Selesaikan semua modul untuk membuka insight karir berbasis AI yang lebih mendalam.
                    @else
                        Profil Anda sudah optimal. AI kami kini memberikan rekomendasi dengan presisi maksimum.
                    @endif
                </p>
            </div>

            @if(Auth::user()->profile?->cv_career_category)
            <!-- AI CV Classification Card -->
            <div class="glass-dark p-8 border-l-4 border-purple-500 animate-fade-in" style="animation-delay: 350ms">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-white"><i class="fas fa-brain text-purple-400 mr-2"></i> Prediksi Karir AI (CV)</h3>
                    <span class="text-[9px] font-black uppercase tracking-widest px-2 py-0.5 rounded bg-purple-500/20 text-purple-400 border border-purple-500/30">
                        Deep Learning
                    </span>
                </div>
                <p class="text-xs text-slate-500 mb-3">Berdasarkan struktur teks dan keahlian di CV Anda:</p>
                <div class="p-4 rounded-xl bg-purple-500/5 border border-purple-500/10 flex items-center justify-between">
                    <div>
                        <span class="text-[9px] text-slate-500 font-bold uppercase tracking-wider">Kategori Terdeteksi</span>
                        <p class="text-base font-extrabold text-white mt-0.5">{{ Auth::user()->profile->cv_career_category }}</p>
                        @php
                            $labelFriendlyNames = [
                                "ACCOUNTANT" => "Akuntan / Keuangan",
                                "ADVOCATE" => "Advokat / Hukum",
                                "AGRICULTURE" => "Pertanian & Agronomi",
                                "APPAREL" => "Mode & Pakaian",
                                "ARTS" => "Seni & Industri Kreatif",
                                "AUTOMOBILE" => "Teknik Otomotif",
                                "AVIATION" => "Penerbangan / Dirgantara",
                                "BANKING" => "Perbankan / Layanan Finansial",
                                "BPO" => "BPO & Customer Service",
                                "BUSINESS-DEVELOPMENT" => "Pengembangan Bisnis",
                                "CHEF" => "Kulinari & Tata Boga",
                                "CONSTRUCTION" => "Konstruksi / Sipil",
                                "CONSULTANT" => "Konsultan Bisnis",
                                "DESIGNER" => "Desain Grafis / UI/UX",
                                "DIGITAL-MEDIA" => "Media Digital & Periklanan",
                                "ENGINEERING" => "Rekayasa & Teknik Umum",
                                "FINANCE" => "Keuangan & Analis Finansial",
                                "FITNESS" => "Kebugaran & Kesehatan",
                                "HEALTHCARE" => "Layanan Kesehatan & Medis",
                                "HR" => "Sumber Daya Manusia (HRD)",
                                "INFORMATION-TECHNOLOGY" => "Teknologi Informasi & Software",
                                "PUBLIC-RELATIONS" => "Hubungan Masyarakat (PR)",
                                "SALES" => "Penjualan & Pemasaran",
                                "TEACHER" => "Pendidik & Guru"
                            ];
                            $friendly = $labelFriendlyNames[Auth::user()->profile->cv_career_category] ?? Auth::user()->profile->cv_career_category;
                        @endphp
                        <span class="text-xs text-purple-400 font-medium">{{ $friendly }}</span>
                    </div>
                    <div class="text-right">
                        <span class="text-[9px] text-slate-500 font-bold uppercase tracking-wider">Confidence</span>
                        <p class="text-xl font-black text-purple-400 mt-0.5">{{ Auth::user()->profile->cv_career_confidence }}%</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Smart Suggestions -->
            <div class="glass-dark p-8 animate-fade-in" style="animation-delay: 400ms">
                <h3 class="text-lg font-bold text-white mb-6">Saran Karir AI</h3>
                <div class="space-y-4">
                    @if(count($topMatches) > 1)
                        @foreach(array_slice($topMatches, 1, 3) as $match)
                        <div class="flex items-center gap-4 p-3 rounded-xl hover:bg-slate-800 transition-colors cursor-pointer group">
                            <div class="w-10 h-10 bg-slate-800 rounded-lg flex items-center justify-center text-slate-500 group-hover:bg-blue-600/20 group-hover:text-blue-500 transition-all">
                                <i class="fas fa-lightbulb"></i>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-white">Pertimbangkan {{ $match['job_title'] }}</p>
                                <p class="text-[10px] text-slate-500 uppercase tracking-widest">{{ $match['confidence'] }}% Skor Kecocokan</p>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-6">
                            <i class="fas fa-lightbulb text-slate-700 text-2xl mb-3"></i>
                            <p class="text-sm text-slate-500">Selesaikan Tes DNA Karir untuk mendapatkan saran personal.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="glass-dark p-8 animate-fade-in" style="animation-delay: 500ms">
        <div class="flex items-center justify-between mb-8">
            <h3 class="text-xl font-bold text-white">Riwayat Rekomendasi Terbaru</h3>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @forelse($recentRecommendations as $rec)
            <div class="p-5 rounded-2xl bg-slate-800/30 border border-slate-700/50 card-hover">
                <p class="text-[10px] font-bold text-blue-500 uppercase tracking-widest mb-2">{{ $rec->job->category->name ?? 'General' }}</p>
                <h4 class="text-sm font-bold text-white mb-4 line-clamp-1">{{ $rec->job->title }}</h4>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-slate-500">{{ $rec->created_at->format('M d') }}</span>
                    <span class="text-xs font-bold text-white">{{ number_format($rec->confidence, 0) }}% CF</span>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-12 text-slate-600">
                <p>Belum ada riwayat rekomendasi. Ikuti Tes DNA Karir atau unggah CV untuk memulai.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

@push('scripts')
<script>
    @if($personalityScores->count() > 0)
    const radarCtx = document.getElementById('personalityRadar').getContext('2d');
    
    const labels = {!! json_encode($personalityScores->pluck('category.name')) !!};
    const scores = {!! json_encode($personalityScores->pluck('score')) !!};

    new Chart(radarCtx, {
        type: 'radar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Profil Kepribadian',
                data: scores,
                backgroundColor: 'rgba(37, 99, 235, 0.2)',
                borderColor: 'rgba(37, 99, 235, 1)',
                borderWidth: 2,
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
                    pointLabels: { 
                        color: '#94a3b8', 
                        font: { size: 11, weight: '600' } 
                    },
                    ticks: { display: false, stepSize: 1 },
                    min: 0,
                    max: 5
                }
            },
            plugins: {
                legend: { display: false }
            },
            responsive: true,
            maintainAspectRatio: false
        }
    });
    @endif
</script>
@endpush
@endsection
