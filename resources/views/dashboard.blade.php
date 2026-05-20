@extends('layouts.app')

@section('title', 'Your Career Companion')

@section('content')
<div class="space-y-8">
    <!-- Welcome Card & Match Summary -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 relative overflow-hidden glass p-8 sm:p-10 rounded-3xl animate-fade-in">
            <div class="relative z-10">
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-white mb-4">Welcome back, {{ Auth::user()->name }}! 👋</h1>
                <p class="text-slate-400 text-base sm:text-lg max-w-xl leading-relaxed">
                    "The best way to predict your future is to create it." 
                    <br>Let's discover your ideal career path today.
                </p>
                
                <div class="mt-8 flex flex-wrap gap-4">
                    @if($progress > 0)
                    <a href="{{ route('assessment.index') }}" class="btn-premium px-8 py-3">
                        <i class="fas fa-wand-magic-sparkles mr-2"></i> Retake Assessment
                    </a>
                    @else
                    <a href="{{ route('assessment.index') }}" class="btn-premium px-8 py-3">
                        <i class="fas fa-dna mr-2"></i> Start Career DNA Test
                    </a>
                    @endif
                    <a href="{{ route('jobs.index') }}" class="px-8 py-3 rounded-xl bg-slate-800 hover:bg-slate-700 transition-all text-white font-semibold">
                        Explore Open Roles
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
            <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-6">Top Career Match</h3>
            @if(count($topMatches) > 0)
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-white mb-1">{{ $topMatches[0]['job_title'] }}</h2>
                    <p class="text-emerald-500 font-bold text-lg">{{ $topMatches[0]['confidence'] }}% Compatibility</p>
                </div>
                <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 mb-6">
                    <p class="text-sm text-slate-300 italic">"{{ $topMatches[0]['explanation'] }}"</p>
                </div>
                @php $topJob = \App\Models\JobListing::find($topMatches[0]['job_id']); @endphp
                @if($topJob)
                <a href="{{ route('jobs.show', $topJob->slug) }}" class="text-blue-500 font-bold text-sm hover:underline">
                    View Role Requirements <i class="fas fa-arrow-right ml-1"></i>
                </a>
                @endif
            @else
                <div class="text-center py-8">
                    <div class="w-16 h-16 mx-auto bg-slate-800 rounded-2xl flex items-center justify-center mb-4">
                        <i class="fas fa-compass text-slate-600 text-2xl"></i>
                    </div>
                    <p class="text-slate-500 text-sm mb-4">Complete the Career DNA Test to see your top match.</p>
                    <a href="{{ route('assessment.index') }}" class="text-blue-500 text-sm font-bold hover:underline">
                        Take the Test <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            @endif
        </div>
    </div>

    {{-- ── Sponsored / Featured Partners Slider ──────────────────────────────── --}}
    <div class="relative overflow-hidden rounded-2xl animate-fade-in" style="animation-delay: 150ms;"
         x-data="{
            current: 0,
            total: 5,
            autoplay: null,
            next() { this.current = (this.current + 1) % this.total; },
            prev() { this.current = (this.current - 1 + this.total) % this.total; },
            goto(i) { this.current = i; },
            startAutoplay() { this.autoplay = setInterval(() => this.next(), 4000); },
            stopAutoplay()  { clearInterval(this.autoplay); }
         }"
         x-init="startAutoplay()"
         @mouseenter="stopAutoplay()"
         @mouseleave="startAutoplay()">

        {{-- Sponsored badge --}}
        <div class="absolute top-3 left-4 z-20">
            <span class="text-[9px] font-bold uppercase tracking-widest px-2 py-0.5 rounded-full bg-black/30 text-white/60 backdrop-blur-sm border border-white/10">
                Sponsored
            </span>
        </div>

        {{-- Arrows --}}
        <button @click="prev()" class="absolute left-3 top-1/2 -translate-y-1/2 z-20 w-9 h-9 rounded-full bg-black/30 hover:bg-black/50 backdrop-blur-sm text-white flex items-center justify-center transition-all border border-white/10 hover:scale-110">
            <i class="fas fa-chevron-left text-xs"></i>
        </button>
        <button @click="next()" class="absolute right-3 top-1/2 -translate-y-1/2 z-20 w-9 h-9 rounded-full bg-black/30 hover:bg-black/50 backdrop-blur-sm text-white flex items-center justify-center transition-all border border-white/10 hover:scale-110">
            <i class="fas fa-chevron-right text-xs"></i>
        </button>

        {{-- Slides --}}
        <div class="relative h-36 sm:h-44">

            {{-- 1: Ganesha Operation --}}
            <div x-show="current===0" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 -translate-x-8"
                 class="absolute inset-0 flex items-center px-8 sm:px-14" style="background: linear-gradient(135deg,#1a1035,#2d1b6b 50%,#1a1035);">
                <div class="absolute inset-0 opacity-20" style="background:radial-gradient(circle at 20% 50%,#7c3aed,transparent 50%),radial-gradient(circle at 80% 50%,#4f46e5,transparent 50%)"></div>
                <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center gap-4 sm:gap-8 w-full">
                    <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-purple-500/30 border border-purple-400/40 flex items-center justify-center shrink-0 shadow-lg shadow-purple-500/20">
                        <i class="fas fa-graduation-cap text-purple-300 text-2xl"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[10px] uppercase tracking-widest text-purple-300 font-bold mb-1">Kursus Terbaik • Ganesha Operation</p>
                        <h3 class="text-lg sm:text-2xl font-extrabold text-white leading-tight mb-1">Raih Nilai A+ dengan Bimbel GO!</h3>
                        <p class="text-slate-300 text-xs sm:text-sm hidden sm:block">Program intensif SD, SMP, SMA & SBMPTN. Lebih dari 1 juta siswa telah sukses bersama kami.</p>
                    </div>
                    <a href="#" class="shrink-0 px-5 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-500 text-white text-sm font-bold transition-all shadow-lg whitespace-nowrap">Daftar Sekarang <i class="fas fa-arrow-right ml-1 text-xs"></i></a>
                </div>
            </div>

            {{-- 2: Gramedia --}}
            <div x-show="current===1" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 -translate-x-8"
                 class="absolute inset-0 flex items-center px-8 sm:px-14" style="background: linear-gradient(135deg,#0c2340,#083d77 50%,#0c2340); display:none">
                <div class="absolute inset-0 opacity-20" style="background:radial-gradient(circle at 20% 50%,#0ea5e9,transparent 50%),radial-gradient(circle at 80% 50%,#06b6d4,transparent 50%)"></div>
                <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center gap-4 sm:gap-8 w-full">
                    <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-sky-500/30 border border-sky-400/40 flex items-center justify-center shrink-0 shadow-lg shadow-sky-500/20">
                        <i class="fas fa-book-open text-sky-300 text-2xl"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[10px] uppercase tracking-widest text-sky-300 font-bold mb-1">Toko Buku • Gramedia</p>
                        <h3 class="text-lg sm:text-2xl font-extrabold text-white leading-tight mb-1">Buku-Buku Terlaris untuk Karir Impianmu</h3>
                        <p class="text-slate-300 text-xs sm:text-sm hidden sm:block">Diskon hingga 40% untuk buku IT, Bisnis, dan Pengembangan Diri. Gratis ongkir se-Indonesia!</p>
                    </div>
                    <a href="#" class="shrink-0 px-5 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-500 text-white text-sm font-bold transition-all shadow-lg whitespace-nowrap">Belanja Sekarang <i class="fas fa-arrow-right ml-1 text-xs"></i></a>
                </div>
            </div>

            {{-- 3: Dicoding --}}
            <div x-show="current===2" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 -translate-x-8"
                 class="absolute inset-0 flex items-center px-8 sm:px-14" style="background: linear-gradient(135deg,#0d2b1f,#064e3b 50%,#0d2b1f); display:none">
                <div class="absolute inset-0 opacity-20" style="background:radial-gradient(circle at 20% 50%,#10b981,transparent 50%),radial-gradient(circle at 80% 50%,#059669,transparent 50%)"></div>
                <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center gap-4 sm:gap-8 w-full">
                    <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-emerald-500/30 border border-emerald-400/40 flex items-center justify-center shrink-0 shadow-lg shadow-emerald-500/20">
                        <i class="fas fa-laptop-code text-emerald-300 text-2xl"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[10px] uppercase tracking-widest text-emerald-300 font-bold mb-1">Kursus Online • Dicoding Indonesia</p>
                        <h3 class="text-lg sm:text-2xl font-extrabold text-white leading-tight mb-1">Kuasai Coding & Dapatkan Sertifikat!</h3>
                        <p class="text-slate-300 text-xs sm:text-sm hidden sm:block">Belajar Web, Android, Machine Learning, dan Cloud dari nol. Diakui oleh Google & AWS.</p>
                    </div>
                    <a href="#" class="shrink-0 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-bold transition-all shadow-lg whitespace-nowrap">Mulai Gratis <i class="fas fa-arrow-right ml-1 text-xs"></i></a>
                </div>
            </div>

            {{-- 4: Ruangguru --}}
            <div x-show="current===3" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 -translate-x-8"
                 class="absolute inset-0 flex items-center px-8 sm:px-14" style="background: linear-gradient(135deg,#2d0a0a,#7f1d1d 50%,#2d0a0a); display:none">
                <div class="absolute inset-0 opacity-20" style="background:radial-gradient(circle at 20% 50%,#ef4444,transparent 50%),radial-gradient(circle at 80% 50%,#f97316,transparent 50%)"></div>
                <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center gap-4 sm:gap-8 w-full">
                    <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-red-500/30 border border-red-400/40 flex items-center justify-center shrink-0 shadow-lg shadow-red-500/20">
                        <i class="fas fa-school text-red-300 text-2xl"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[10px] uppercase tracking-widest text-red-300 font-bold mb-1">Platform Belajar • Ruangguru</p>
                        <h3 class="text-lg sm:text-2xl font-extrabold text-white leading-tight mb-1">Belajar Lebih Cerdas bersama AI Tutor!</h3>
                        <p class="text-slate-300 text-xs sm:text-sm hidden sm:block">Ribuan video pelajaran, soal latihan, dan bimbingan langsung dari guru terbaik Indonesia.</p>
                    </div>
                    <a href="#" class="shrink-0 px-5 py-2.5 rounded-xl bg-red-600 hover:bg-red-500 text-white text-sm font-bold transition-all shadow-lg whitespace-nowrap">Coba Gratis <i class="fas fa-arrow-right ml-1 text-xs"></i></a>
                </div>
            </div>

            {{-- 5: Udemy --}}
            <div x-show="current===4" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 -translate-x-8"
                 class="absolute inset-0 flex items-center px-8 sm:px-14" style="background: linear-gradient(135deg,#1c1200,#78350f 50%,#1c1200); display:none">
                <div class="absolute inset-0 opacity-20" style="background:radial-gradient(circle at 20% 50%,#f59e0b,transparent 50%),radial-gradient(circle at 80% 50%,#d97706,transparent 50%)"></div>
                <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center gap-4 sm:gap-8 w-full">
                    <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-amber-500/30 border border-amber-400/40 flex items-center justify-center shrink-0 shadow-lg shadow-amber-500/20">
                        <i class="fas fa-certificate text-amber-300 text-2xl"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[10px] uppercase tracking-widest text-amber-300 font-bold mb-1">Kursus Global • Udemy</p>
                        <h3 class="text-lg sm:text-2xl font-extrabold text-white leading-tight mb-1">210.000+ Kursus. Belajar Apa Saja!</h3>
                        <p class="text-slate-300 text-xs sm:text-sm hidden sm:block">Data Science, UI/UX, Business, Programming — pilih kursusmu dan mulai karir impian hari ini.</p>
                    </div>
                    <a href="#" class="shrink-0 px-5 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-500 text-white text-sm font-bold transition-all shadow-lg whitespace-nowrap">Lihat Kursus <i class="fas fa-arrow-right ml-1 text-xs"></i></a>
                </div>
            </div>

        </div>

        {{-- Dot indicators --}}
        <div class="absolute bottom-3 left-1/2 -translate-x-1/2 flex items-center gap-1.5 z-20">
            @for($i = 0; $i < 5; $i++)
            <button @click="goto({{ $i }})" class="rounded-full transition-all duration-300" :class="current === {{ $i }} ? 'w-5 h-1.5 bg-white' : 'w-1.5 h-1.5 bg-white/30 hover:bg-white/60'"></button>
            @endfor
        </div>

    </div>
    {{-- ── End Ads Slider ──────────────────────────────────────────────────────── --}}

    <!-- Analytics Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Skill Radar Chart -->
        <div class="glass-dark p-8 animate-fade-in" style="animation-delay: 200ms">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="text-xl font-bold text-white">Personality Insights</h3>
                    <p class="text-xs text-slate-500 mt-1">Based on your recent assessment scores</p>
                </div>
                <div class="w-10 h-10 bg-purple-500/10 rounded-xl flex items-center justify-center text-purple-500">
                    <i class="fas fa-chart-pie"></i>
                </div>
            </div>
            @if($personalityScores->count() > 0)
            <div class="h-80">
                <canvas id="personalityRadar"></canvas>
            </div>
            @else
            <div class="text-center py-12">
                <div class="w-16 h-16 mx-auto bg-slate-800 rounded-2xl flex items-center justify-center mb-4">
                    <i class="fas fa-chart-bar text-slate-600 text-2xl"></i>
                </div>
                <p class="text-slate-500 text-sm">No personality data yet. Take the Career DNA Test to see your chart.</p>
            </div>
            @endif
        </div>

        <!-- Progress & Suggestions -->
        <div class="space-y-8">
            <!-- Assessment Progress -->
            <div class="glass-dark p-8 animate-fade-in" style="animation-delay: 300ms">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-white">Profile Strength</h3>
                    <span class="text-blue-500 font-bold text-sm">{{ round($progress) }}%</span>
                </div>
                <div class="w-full bg-slate-800 h-3 rounded-full overflow-hidden mb-6">
                    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 h-full transition-all duration-1000" style="width: {{ $progress }}%"></div>
                </div>
                <p class="text-xs text-slate-500 leading-relaxed">
                    @if($progress == 0)
                        Start the Career DNA Test to build your profile and unlock AI-driven insights.
                    @elseif($progress < 100)
                        Complete all modules to unlock deeper AI-driven career insights.
                    @else
                        Your profile is fully optimized. Our AI is now delivering maximum precision matches.
                    @endif
                </p>
            </div>

            <!-- Smart Suggestions -->
            <div class="glass-dark p-8 animate-fade-in" style="animation-delay: 400ms">
                <h3 class="text-lg font-bold text-white mb-6">AI Career Suggestions</h3>
                <div class="space-y-4">
                    @if(count($topMatches) > 1)
                        @foreach(array_slice($topMatches, 1, 3) as $match)
                        <div class="flex items-center gap-4 p-3 rounded-xl hover:bg-slate-800 transition-colors cursor-pointer group">
                            <div class="w-10 h-10 bg-slate-800 rounded-lg flex items-center justify-center text-slate-500 group-hover:bg-blue-600/20 group-hover:text-blue-500 transition-all">
                                <i class="fas fa-lightbulb"></i>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-white">Consider {{ $match['job_title'] }}</p>
                                <p class="text-[10px] text-slate-500 uppercase tracking-widest">{{ $match['confidence'] }}% Match Score</p>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-6">
                            <i class="fas fa-lightbulb text-slate-700 text-2xl mb-3"></i>
                            <p class="text-sm text-slate-500">Complete the Career DNA Test to get personalized suggestions.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="glass-dark p-8 animate-fade-in" style="animation-delay: 500ms">
        <div class="flex items-center justify-between mb-8">
            <h3 class="text-xl font-bold text-white">Recent Recommendation History</h3>
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
                <p>No recommendation history yet. Take the Career DNA Test or upload your CV to get started.</p>
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
                label: 'Personality Profile',
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
                        font: { size: 12, weight: '600' } 
                    },
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
    @endif
</script>
@endpush
@endsection
