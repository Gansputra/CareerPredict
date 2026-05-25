<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="{ theme: localStorage.getItem('theme') || 'dark' }"
      :class="{ 'light-mode': theme === 'light' }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>CareerPredict | AI-Powered Job Recommendations</title>
        
        <!-- Favicon / Web App Icon -->
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}?v=2">
        
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased selection:bg-blue-500/30">
        <!-- Navigation -->
        <nav class="fixed w-full z-50 top-0 px-4 sm:px-6 py-4 transition-all duration-300" x-data="{ scrolled: false, mobileMenu: false }" @scroll.window="scrolled = (window.pageYOffset > 20)">
            <div class="max-w-7xl mx-auto flex items-center justify-between transition-all duration-300 px-3 sm:px-6 py-3 rounded-2xl overflow-hidden" :class="scrolled ? 'glass shadow-2xl py-2' : ''">
                <div class="flex items-center gap-2 shrink-0">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-600/40">
                        <i class="fas fa-brain text-white text-base sm:text-xl"></i>
                    </div>
                    <span class="text-lg sm:text-2xl font-bold tracking-tight text-white">Career<span class="text-blue-500">Predict</span></span>
                </div>
                
                <div class="hidden md:flex items-center gap-8 text-sm font-medium">
                    <a href="#features" class="hover:text-blue-400 transition-colors">Fitur</a>
                    <a href="#how-it-works" class="hover:text-blue-400 transition-colors">Metodologi</a>
                    <a href="{{ route('jobs.index') }}" class="hover:text-blue-400 transition-colors">Lowongan</a>
                </div>

                <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn-premium py-1.5 px-3 sm:py-2 sm:px-5 text-xs sm:text-sm">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="text-xs sm:text-sm font-medium hover:text-blue-400 transition-colors hidden sm:inline">Masuk</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn-premium py-1.5 px-3 sm:py-2 sm:px-5 text-xs sm:text-sm whitespace-nowrap hidden sm:inline-block">Mulai Sekarang</a>
                            @endif
                        @endauth
                    @endif

                    <!-- Mobile Menu Button -->
                    <button @click="mobileMenu = !mobileMenu" class="md:hidden p-1.5 sm:p-2 rounded-lg hover:bg-white/10 transition-colors">
                        <i class="fas text-lg sm:text-xl text-slate-300" :class="mobileMenu ? 'fa-times' : 'fa-bars'"></i>
                    </button>
                </div>
            </div>

            <!-- Mobile Dropdown Menu -->
            <div x-show="mobileMenu"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 @click.away="mobileMenu = false"
                 class="md:hidden mt-2 mx-4 sm:mx-6 glass rounded-2xl shadow-2xl overflow-hidden">
                <div class="px-4 py-3 space-y-1">
                    <a href="#features" @click="mobileMenu = false" class="block px-4 py-3 rounded-xl text-slate-300 hover:bg-white/10 hover:text-white transition-all font-medium text-sm">
                        <i class="fas fa-sparkles mr-2 w-5 text-center"></i> Fitur
                    </a>
                    <a href="#how-it-works" @click="mobileMenu = false" class="block px-4 py-3 rounded-xl text-slate-300 hover:bg-white/10 hover:text-white transition-all font-medium text-sm">
                        <i class="fas fa-flask mr-2 w-5 text-center"></i> Metodologi
                    </a>
                    <a href="{{ route('jobs.index') }}" class="block px-4 py-3 rounded-xl text-slate-300 hover:bg-white/10 hover:text-white transition-all font-medium text-sm">
                        <i class="fas fa-briefcase mr-2 w-5 text-center"></i> Lowongan
                    </a>
                    @guest
                    <div class="pt-2 mt-2 border-t border-white/10">
                        <a href="{{ route('login') }}" class="block px-4 py-3 rounded-xl text-slate-300 hover:bg-white/10 hover:text-white transition-all font-medium text-sm">
                            <i class="fas fa-sign-in-alt mr-2 w-5 text-center"></i> Masuk
                        </a>
                    </div>
                    @endguest
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="relative min-h-screen flex items-center justify-center pt-20 overflow-hidden gradient-bg">
            <div class="absolute inset-0 z-0">
                <div class="absolute top-1/4 -left-20 w-96 h-96 bg-blue-600/20 rounded-full blur-[120px]"></div>
                <div class="absolute bottom-1/4 -right-20 w-96 h-96 bg-emerald-600/20 rounded-full blur-[120px]"></div>
            </div>

            <div class="container mx-auto px-6 relative z-10 text-center">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full glass border-white/10 mb-8 animate-bounce" data-aos="fade-down">
                    <span class="flex h-2 w-2 rounded-full bg-blue-500"></span>
                    <span class="text-xs font-semibold tracking-wider uppercase text-blue-400">Didukung Metode Certainty Factor</span>
                </div>
                
                <h1 class="text-5xl md:text-8xl font-extrabold mb-6 leading-tight" data-aos="fade-up">
                    Temukan Jalur <br>
                    <span class="text-gradient">Karir Idealmu</span>
                </h1>
                
                <p class="text-xl text-slate-400 max-w-2xl mx-auto mb-10 leading-relaxed" data-aos="fade-up" data-aos-delay="100">
                    Sistem pakar AI kami menggunakan metode Certainty Factor untuk mencocokkan keahlian dan minat Anda dengan peluang kerja terbaik.
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center gap-4" data-aos="fade-up" data-aos-delay="200">
                    <a href="{{ route('register') }}" class="btn-premium w-full sm:w-auto px-10">Mulai Asesmen</a>
                    <a href="#how-it-works" class="px-10 py-3 rounded-xl border border-slate-700 hover:bg-slate-800 transition-all font-semibold">Pelajari Selengkapnya</a>
                </div>

                <!-- Floating Elements -->
                <div class="mt-20 grid grid-cols-2 md:grid-cols-4 gap-6 max-w-5xl mx-auto">
                    @foreach($stats as $label => $value)
                    <div class="glass p-6 text-center card-hover" data-aos="zoom-in" data-aos-delay="{{ $loop->index * 100 }}">
                        <div class="text-3xl font-bold text-white mb-1">{{ number_format($value) }}+</div>
                        <div class="text-xs text-slate-500 uppercase tracking-widest font-semibold">{{ str_replace('_', ' ', $label) }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- Features -->
        <section id="features" class="py-24 bg-slate-900/50">
            <div class="container mx-auto px-6 text-center mb-16">
                <h2 class="text-3xl md:text-5xl font-bold mb-4" data-aos="fade-up">Analisis Karir Cerdas</h2>
                <p class="text-slate-500" data-aos="fade-up" data-aos-delay="100">Fitur canggih yang dirancang untuk membantu Anda sukses di dunia kerja modern.</p>
            </div>

            <div class="container mx-auto px-6 grid md:grid-cols-3 gap-8">
                <div class="glass p-8 card-hover" data-aos="fade-up">
                    <div class="w-14 h-14 bg-blue-500/10 rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-microchip text-blue-500 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-4 text-white">Rekomendasi CF</h3>
                    <p class="text-slate-400 leading-relaxed">Menggunakan logika certainty factor untuk menggabungkan berbagai bukti dan memberikan skor pencocokan pekerjaan yang akurat.</p>
                </div>
                
                <div class="glass p-8 card-hover" data-aos="fade-up" data-aos-delay="100">
                    <div class="w-14 h-14 bg-emerald-500/10 rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-chart-pie text-emerald-500 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-4 text-white">Pemetaan Keahlian</h3>
                    <p class="text-slate-400 leading-relaxed">Visualisasikan kesenjangan keahlian Anda dan identifikasi apa yang perlu dipelajari untuk karir impian.</p>
                </div>

                <div class="glass p-8 card-hover" data-aos="fade-up" data-aos-delay="200">
                    <div class="w-14 h-14 bg-purple-500/10 rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-briefcase text-purple-500 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-4 text-white">Pencocokan Real-time</h3>
                    <p class="text-slate-400 leading-relaxed">Langsung cocokkan dengan lowongan kerja aktif dari database mitra perusahaan kami yang luas.</p>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="py-12 border-t border-slate-800">
            <div class="container mx-auto px-6 flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-brain text-white text-sm"></i>
                    </div>
                    <span class="font-bold text-white uppercase tracking-wider">CareerPredict</span>
                </div>
                <div class="text-slate-500 text-sm">
                    &copy; 2026 CareerPredict. Dibangun untuk Keunggulan.
                </div>
                <div class="flex gap-6">
                    <a href="#" class="text-slate-400 hover:text-white transition-colors"><i class="fab fa-github"></i></a>
                    <a href="#" class="text-slate-400 hover:text-white transition-colors"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="text-slate-400 hover:text-white transition-colors"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>
        </footer>

        <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
        <script>
            AOS.init({
                duration: 1000,
                once: true,
            });
        </script>
    </body>
</html>
