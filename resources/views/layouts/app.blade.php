<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'CareerPredict') }}</title>
        
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
          x-init="$watch('theme', val => localStorage.setItem('theme', val))"
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
            <aside class="fixed inset-y-0 left-0 z-50 w-72 transition-all duration-300 transform bg-[#1e293b] border-r border-slate-800 overflow-y-auto"
                   :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
                <div class="flex items-center justify-between px-6 py-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-600/40">
                            <i class="fas fa-brain text-white text-xl"></i>
                        </div>
                        <span class="text-2xl font-bold tracking-tight text-white">Career<span class="text-blue-500">Predict</span></span>
                    </div>
                    <!-- Close button (mobile only) -->
                    <button @click="sidebarOpen = false" class="lg:hidden p-2 rounded-lg hover:bg-slate-800 transition-colors">
                        <i class="fas fa-times text-slate-400"></i>
                    </button>
                </div>

                <nav class="px-4 space-y-2 pb-28">
                    <p class="px-4 text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-4">Main Menu</p>
                    
                    <a href="{{ route('dashboard') }}" @click="sidebarOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fas fa-th-large text-lg w-6"></i>
                        <span class="font-medium">Dashboard</span>
                    </a>

                    <a href="{{ route('assessment.index') }}" @click="sidebarOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('assessment.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fas fa-wand-magic-sparkles text-lg w-6"></i>
                        <span class="font-medium">Career DNA Test</span>
                    </a>

                    <a href="{{ route('jobs.index') }}" @click="sidebarOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('jobs.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fas fa-briefcase text-lg w-6"></i>
                        <span class="font-medium">Job Explorer</span>
                    </a>

                    <a href="{{ route('cv.index') }}" @click="sidebarOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('cv.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fas fa-file-pdf text-lg w-6"></i>
                        <span class="font-medium">CV Analyzer</span>
                    </a>

                    @if(Auth::user()->isAdmin())
                    <p class="px-4 text-[10px] font-bold uppercase tracking-widest text-slate-500 mt-8 mb-4">Admin Panel</p>
                    <a href="{{ route('admin.dashboard') }}" @click="sidebarOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-purple-600 text-white shadow-lg shadow-purple-600/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fas fa-chart-line text-lg w-6"></i>
                        <span class="font-medium">Admin Dashboard</span>
                    </a>
                    <a href="{{ route('admin.jobs.import') }}" @click="sidebarOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('admin.jobs.import') ? 'bg-slate-700 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fas fa-file-import text-lg w-6"></i>
                        <span class="font-medium">Job Importer</span>
                    </a>
                    @endif

                    <p class="px-4 text-[10px] font-bold uppercase tracking-widest text-slate-500 mt-8 mb-4">Account</p>
                    <a href="{{ route('profile.edit') }}" @click="sidebarOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('profile.*') ? 'bg-slate-700 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fas fa-user-circle text-lg w-6"></i>
                        <span class="font-medium">My Profile</span>
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:bg-red-500/10 hover:text-red-500 transition-all">
                            <i class="fas fa-sign-out-alt text-lg w-6"></i>
                            <span class="font-medium">Logout</span>
                        </button>
                    </form>
                </nav>

                <div class="absolute bottom-0 left-0 w-full px-4 pb-4 bg-[#1e293b]">
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
                        <div class="flex items-center gap-3 sm:gap-6">
                            <div class="relative hidden md:block">
                                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-sm"></i>
                                <input type="text" placeholder="Search anything..." class="bg-slate-900 border-slate-700 rounded-xl pl-10 pr-4 py-2 text-sm focus:ring-blue-500 focus:border-blue-500 text-slate-300 w-48 lg:w-64">
                            </div>

                            <!-- Theme Toggle -->
                            <button @click="theme = theme === 'dark' ? 'light' : 'dark'" class="relative w-10 h-10 flex items-center justify-center rounded-lg hover:bg-slate-800 transition-colors ignore-invert bg-slate-800/50">
                                <i class="fas text-lg" :class="theme === 'dark' ? 'fa-sun text-amber-400' : 'fa-moon text-slate-400'"></i>
                            </button>

                            <button class="relative p-2 rounded-lg hover:bg-slate-800 transition-colors">
                                <i class="fas fa-bell text-slate-400"></i>
                                <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full"></span>
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
