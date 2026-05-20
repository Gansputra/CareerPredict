<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="{ theme: localStorage.getItem('theme') || 'dark' }"
      :class="{ 'light-mode': theme === 'light' }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>CareerPredict | AI-Powered Job Recommendations</title>
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
            <div class="max-w-7xl mx-auto flex items-center justify-between transition-all duration-300 px-4 sm:px-6 py-3" :class="scrolled ? 'glass shadow-2xl py-2' : ''">
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-600/40">
                        <i class="fas fa-brain text-white text-xl"></i>
                    </div>
                    <span class="text-2xl font-bold tracking-tight text-white">Career<span class="text-blue-500">Predict</span></span>
                </div>
                
                <div class="hidden md:flex items-center gap-8 text-sm font-medium">
                    <a href="#features" class="hover:text-blue-400 transition-colors">Features</a>
                    <a href="#how-it-works" class="hover:text-blue-400 transition-colors">Methodology</a>
                    <a href="{{ route('jobs.index') }}" class="hover:text-blue-400 transition-colors">Jobs</a>
                </div>

                <div class="flex items-center gap-3">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn-premium py-2 px-5 text-sm">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="text-sm font-medium hover:text-blue-400 transition-colors hidden sm:inline">Log in</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn-premium py-2 px-5 text-sm">Get Started</a>
                            @endif
                        @endauth
                    @endif

                    <!-- Mobile Menu Button -->
                    <button @click="mobileMenu = !mobileMenu" class="md:hidden p-2 rounded-lg hover:bg-white/10 transition-colors">
                        <i class="fas text-xl text-slate-300" :class="mobileMenu ? 'fa-times' : 'fa-bars'"></i>
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
                        <i class="fas fa-sparkles mr-2 w-5 text-center"></i> Features
                    </a>
                    <a href="#how-it-works" @click="mobileMenu = false" class="block px-4 py-3 rounded-xl text-slate-300 hover:bg-white/10 hover:text-white transition-all font-medium text-sm">
                        <i class="fas fa-flask mr-2 w-5 text-center"></i> Methodology
                    </a>
                    <a href="{{ route('jobs.index') }}" class="block px-4 py-3 rounded-xl text-slate-300 hover:bg-white/10 hover:text-white transition-all font-medium text-sm">
                        <i class="fas fa-briefcase mr-2 w-5 text-center"></i> Jobs
                    </a>
                    @guest
                    <div class="pt-2 mt-2 border-t border-white/10">
                        <a href="{{ route('login') }}" class="block px-4 py-3 rounded-xl text-slate-300 hover:bg-white/10 hover:text-white transition-all font-medium text-sm">
                            <i class="fas fa-sign-in-alt mr-2 w-5 text-center"></i> Log in
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
                    <span class="text-xs font-semibold tracking-wider uppercase text-blue-400">Powered by Certainty Factor Method</span>
                </div>
                
                <h1 class="text-5xl md:text-8xl font-extrabold mb-6 leading-tight" data-aos="fade-up">
                    Find Your Perfect <br>
                    <span class="text-gradient">Career Path</span>
                </h1>
                
                <p class="text-xl text-slate-400 max-w-2xl mx-auto mb-10 leading-relaxed" data-aos="fade-up" data-aos-delay="100">
                    Our AI expert system uses the Certainty Factor method to match your skills and interests with the world's best job opportunities.
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center gap-4" data-aos="fade-up" data-aos-delay="200">
                    <a href="{{ route('register') }}" class="btn-premium w-full sm:w-auto px-10">Start Assessment</a>
                    <a href="#how-it-works" class="px-10 py-3 rounded-xl border border-slate-700 hover:bg-slate-800 transition-all font-semibold">Learn More</a>
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
                <h2 class="text-3xl md:text-5xl font-bold mb-4" data-aos="fade-up">Intelligent Career Analysis</h2>
                <p class="text-slate-500" data-aos="fade-up" data-aos-delay="100">Advanced features designed to help you succeed in the modern job market.</p>
            </div>

            <div class="container mx-auto px-6 grid md:grid-cols-3 gap-8">
                <div class="glass p-8 card-hover" data-aos="fade-up">
                    <div class="w-14 h-14 bg-blue-500/10 rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-microchip text-blue-500 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-4 text-white">CF Recommendation</h3>
                    <p class="text-slate-400 leading-relaxed">Uses certainty factor logic to combine multiple evidences and provide accurate job matching scores.</p>
                </div>
                
                <div class="glass p-8 card-hover" data-aos="fade-up" data-aos-delay="100">
                    <div class="w-14 h-14 bg-emerald-500/10 rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-chart-pie text-emerald-500 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-4 text-white">Skill Mapping</h3>
                    <p class="text-slate-400 leading-relaxed">Visualize your skill gaps and identify what you need to learn for your dream career.</p>
                </div>

                <div class="glass p-8 card-hover" data-aos="fade-up" data-aos-delay="200">
                    <div class="w-14 h-14 bg-purple-500/10 rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-briefcase text-purple-500 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-4 text-white">Real-time Matching</h3>
                    <p class="text-slate-400 leading-relaxed">Instantly match with live job vacancies from our extensive database of partner companies.</p>
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
                    &copy; 2026 CareerPredict. Built for Excellence.
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
