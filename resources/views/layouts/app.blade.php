<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'CareerPredict') }}</title>
        
        <!-- Favicon / Web App Icon -->
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}?v=2">
        
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased selection:bg-blue-500/30 transition-colors duration-300"
          x-data="{ sidebarOpen: false, theme: localStorage.getItem('theme') || 'dark' }" 
          x-init="$watch('theme', val => { localStorage.setItem('theme', val); document.documentElement.className = val === 'light' ? 'light-mode' : ''; }); if(theme === 'light') document.documentElement.className = 'light-mode';"
          :class="{ 'light-mode': theme === 'light' }">
        <div class="min-h-screen bg-[#0f172a]" 
             @keydown.escape.window="sidebarOpen = false">

            <!-- Mobile Overlay -->
            <div x-show="sidebarOpen"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="sidebarOpen = false"
                 class="fixed inset-0 z-40 bg-black/60 backdrop-blur-sm lg:hidden"></div>

            <!-- Sidebar -->
            <aside class="fixed inset-y-0 left-0 z-50 w-72 transition-all duration-300 transform bg-[#1e293b] border-r border-slate-800 flex flex-col"
                   :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
                <div class="flex items-center justify-between px-6 py-6 shrink-0">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 hover:opacity-80 transition-opacity">
                        <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-600/40">
                            <i class="fas fa-brain text-white text-xl"></i>
                        </div>
                        <span class="text-2xl font-bold tracking-tight text-white">Career<span class="text-blue-500">Predict</span></span>
                    </a>
                    <!-- Close button (mobile only) -->
                    <button @click="sidebarOpen = false" class="lg:hidden p-2 rounded-lg hover:bg-slate-800 transition-colors">
                        <i class="fas fa-times text-slate-400"></i>
                    </button>
                </div>

                <nav class="px-4 space-y-2 pb-6 flex-1 overflow-y-auto">
                    <p class="px-4 text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-4">Menu Utama</p>
                    
                    @if(!Auth::user()->isAdmin())
                    <a href="{{ route('dashboard') }}" @click="sidebarOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fas fa-house text-lg w-6"></i>
                        <span class="font-medium">Dashboard</span>
                    </a>

                    <a href="{{ route('assessment.index') }}" @click="sidebarOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('assessment.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fas fa-wand-magic-sparkles text-lg w-6"></i>
                        <span class="font-medium">Tes DNA Karir</span>
                    </a>

                    <a href="{{ route('cv.index') }}" @click="sidebarOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('cv.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fas fa-file-pdf text-lg w-6"></i>
                        <span class="font-medium">Analisis CV</span>
                    </a>

                    <a href="{{ route('jobs.index') }}" @click="sidebarOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('jobs.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fas fa-briefcase text-lg w-6"></i>
                        <span class="font-medium">Jelajahi Lowongan</span>
                    </a>

                    <a href="{{ route('roadmap.index') }}" @click="sidebarOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('roadmap.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fas fa-map-signs text-lg w-6"></i>
                        <span class="font-medium">Peta Belajar</span>
                    </a>

                    <a href="{{ route('salary.index') }}" @click="sidebarOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('salary.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fas fa-money-bill-trend-up text-lg w-6"></i>
                        <span class="font-medium">Info Gaji</span>
                    </a>

                    <a href="{{ route('skillmatrix.index') }}" @click="sidebarOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('skillmatrix.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fas fa-layer-group text-lg w-6"></i>
                        <span class="font-medium">Matriks Keahlian</span>
                    </a>

                    <a href="{{ route('tracker.index') }}" @click="sidebarOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('tracker.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fas fa-clipboard-list text-lg w-6"></i>
                        <span class="font-medium">Pelacak Lamaran</span>
                    </a>

                    <a href="{{ route('interview.index') }}" @click="sidebarOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('interview.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fas fa-microphone-lines text-lg w-6"></i>
                        <span class="font-medium">Simulasi Interview</span>
                    </a>

                    <p class="px-4 text-[10px] font-bold uppercase tracking-widest text-slate-500 mt-8 mb-4">Bantuan</p>

                    <a href="{{ route('docs.index') }}" @click="sidebarOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('docs.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fas fa-book text-lg w-6"></i>
                        <span class="font-medium">Dokumentasi</span>
                    </a>
                    @endif

                    @if(Auth::user()->isAdmin())
                    <p class="px-4 text-[10px] font-bold uppercase tracking-widest text-slate-500 mt-8 mb-4">Panel Admin</p>
                    <a href="{{ route('admin.dashboard') }}" @click="sidebarOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-purple-600 text-white shadow-lg shadow-purple-600/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fas fa-chart-line text-lg w-6"></i>
                        <span class="font-medium">Dashboard Admin</span>
                    </a>
                    <a href="{{ route('admin.jobs.import') }}" @click="sidebarOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('admin.jobs.import') ? 'bg-slate-700 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fas fa-file-import text-lg w-6"></i>
                        <span class="font-medium">Impor Lowongan</span>
                    </a>
                    @endif

                    <p class="px-4 text-[10px] font-bold uppercase tracking-widest text-slate-500 mt-8 mb-4">Akun</p>
                    <a href="{{ route('profile.edit') }}" @click="sidebarOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('profile.*') ? 'bg-slate-700 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fas fa-user-circle text-lg w-6"></i>
                        <span class="font-medium">Profil Saya</span>
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:bg-red-500/10 hover:text-red-500 transition-all">
                            <i class="fas fa-sign-out-alt text-lg w-6"></i>
                            <span class="font-medium">Keluar</span>
                        </button>
                    </form>
                </nav>

                <div class="shrink-0 px-4 pb-5 pt-3 bg-[#1e293b] border-t border-slate-700/60">
                    <div class="glass p-3 rounded-2xl flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-slate-700 overflow-hidden shrink-0">
                            @if(Auth::user()->profile?->avatar)
                                <img src="{{ asset('storage/' . Auth::user()->profile->avatar) }}" alt="Avatar" class="w-full h-full object-cover">
                            @else
                                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=0D8ABC&color=fff" alt="Avatar" class="w-full h-full object-cover">
                            @endif
                        </div>
                        <div class="flex-1 overflow-hidden">
                            <p class="text-xs font-bold text-white truncate">{{ Auth::user()->name }}</p>
                            <p class="text-[10px] text-slate-500 truncate">{{ Auth::user()->email }}</p>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Main Content -->
            <div class="lg:ml-72 transition-all duration-300">
                <!-- Top Header -->
                <header class="sticky top-0 z-30 bg-[#0f172a]/80 backdrop-blur-md border-b border-slate-800 px-4 sm:px-8 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-lg hover:bg-slate-800 transition-colors lg:hidden">
                                <i class="fas fa-bars text-slate-400"></i>
                            </button>
                            <h2 class="text-lg sm:text-xl font-bold text-white truncate">
                                @yield('title', 'Dashboard')
                            </h2>
                        </div>
                        <div class="flex items-center gap-2 sm:gap-4 lg:gap-6">

                            <!-- Theme Toggle -->
                            <button @click="theme = theme === 'dark' ? 'light' : 'dark'" class="relative w-10 h-10 flex items-center justify-center rounded-lg hover:bg-slate-800 transition-all duration-300 bg-slate-800/50" title="Ganti tema">
                                <i class="fas text-lg transition-transform duration-300" :class="theme === 'dark' ? 'fa-sun text-amber-400 rotate-0' : 'fa-moon text-indigo-500 rotate-12'"></i>
                            </button>

                            <!-- Mobile avatar -->
                            <div class="w-8 h-8 rounded-full bg-slate-700 overflow-hidden lg:hidden">
                                @if(Auth::user()->profile?->avatar)
                                    <img src="{{ asset('storage/' . Auth::user()->profile->avatar) }}" alt="Avatar" class="w-full h-full object-cover">
                                @else
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=0D8ABC&color=fff&size=32" alt="Avatar" class="w-full h-full object-cover">
                                @endif
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="p-4 sm:p-6 lg:p-8">
                    @if(isset($header))
                        <div class="mb-8">
                            {{ $header }}
                        </div>
                    @endif
                    
                    {{ $slot ?? '' }}
                    @yield('content')
                </main>
            </div>
        </div>

        <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
        <script>
            AOS.init({
                duration: 800,
                once: true,
            });
        </script>
        @stack('scripts')
    </body>
</html>
