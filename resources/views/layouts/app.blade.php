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
    <body class="antialiased selection:bg-blue-500/30">
        <div class="min-h-screen flex bg-[#0f172a]" x-data="{ sidebarOpen: true }">
            <!-- Sidebar -->
            <aside class="fixed inset-y-0 left-0 z-50 w-72 transition-all duration-300 transform bg-[#1e293b] border-r border-slate-800" :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
                <div class="flex items-center gap-3 px-8 py-8">
                    <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-600/40">
                        <i class="fas fa-brain text-white text-xl"></i>
                    </div>
                    <span class="text-2xl font-bold tracking-tight text-white">Career<span class="text-blue-500">Predict</span></span>
                </div>

                <nav class="px-4 space-y-2">
                    <p class="px-4 text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-4">Main Menu</p>
                    
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fas fa-grid-2 text-lg w-6"></i>
                        <span class="font-medium">Dashboard</span>
                    </a>

                    <a href="{{ route('expert.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('expert.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fas fa-wand-magic-sparkles text-lg w-6"></i>
                        <span class="font-medium">Skill Assessment</span>
                    </a>

                    <a href="{{ route('jobs.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('jobs.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fas fa-briefcase text-lg w-6"></i>
                        <span class="font-medium">Job Explorer</span>
                    </a>

                    @if(Auth::user()->isAdmin())
                    <p class="px-4 text-[10px] font-bold uppercase tracking-widest text-slate-500 mt-8 mb-4">Admin Panel</p>
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-purple-600 text-white shadow-lg shadow-purple-600/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fas fa-chart-line text-lg w-6"></i>
                        <span class="font-medium">Admin Dashboard</span>
                    </a>
                    @endif

                    <p class="px-4 text-[10px] font-bold uppercase tracking-widest text-slate-500 mt-8 mb-4">Account</p>
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('profile.*') ? 'bg-slate-700 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
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

                <div class="absolute bottom-8 left-0 w-full px-6">
                    <div class="glass p-4 rounded-2xl flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-slate-700 overflow-hidden">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=0D8ABC&color=fff" alt="Avatar">
                        </div>
                        <div class="flex-1 overflow-hidden">
                            <p class="text-xs font-bold text-white truncate">{{ Auth::user()->name }}</p>
                            <p class="text-[10px] text-slate-500 truncate">{{ Auth::user()->email }}</p>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Main Content -->
            <div class="flex-1 transition-all duration-300" :class="sidebarOpen ? 'ml-72' : 'ml-0'">
                <!-- Top Header -->
                <header class="sticky top-0 z-40 bg-[#0f172a]/80 backdrop-blur-md border-b border-slate-800 px-8 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-lg hover:bg-slate-800 transition-colors">
                                <i class="fas fa-bars-staggered text-slate-400"></i>
                            </button>
                            <h2 class="text-xl font-bold text-white">
                                @yield('title', 'Dashboard')
                            </h2>
                        </div>
                        <div class="flex items-center gap-6">
                            <div class="relative hidden sm:block">
                                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-sm"></i>
                                <input type="text" placeholder="Search anything..." class="bg-slate-900 border-slate-700 rounded-xl pl-10 pr-4 py-2 text-sm focus:ring-blue-500 focus:border-blue-500 text-slate-300">
                            </div>
                            <button class="relative p-2 rounded-lg hover:bg-slate-800 transition-colors">
                                <i class="fas fa-bell text-slate-400"></i>
                                <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full"></span>
                            </button>
                        </div>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="p-8">
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
