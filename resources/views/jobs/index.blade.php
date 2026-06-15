@extends('layouts.app')

@section('title', 'Jelajahi Lowongan')

@section('content')
<div class="space-y-8">
    <!-- Filter Section -->
    <div class="glass p-4 relative z-30" x-data="{ 
        showFilters: {{ (request('category') || request('location') || (request('sort') && request('sort') !== 'date_desc')) ? 'true' : 'false' }},
        selectedCategory: '{{ request('category') }}',
        selectedLocation: '{{ request('location') }}',
        selectedSort: '{{ request('sort', 'date_desc') }}'
    }">
        <form action="{{ route('jobs.index') }}" method="GET" class="space-y-4">
            <!-- Main Search Bar Row -->
            <div class="flex flex-col sm:flex-row gap-3">
                <!-- Search Input -->
                <div class="relative flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari berdasarkan judul, perusahaan, atau kata kunci..." class="w-full bg-slate-900 border border-slate-700 rounded-xl pl-10 pr-4 py-2.5 text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all search-input">
                    <i class="fas fa-search text-slate-500 text-sm search-icon"></i>
                </div>
                
                <!-- Toggle Filter Button & Search Button -->
                <div class="flex gap-2">
                    <button type="button" @click="showFilters = !showFilters" 
                            class="px-4 py-2.5 rounded-xl border border-slate-700 text-sm font-semibold transition-all flex items-center gap-2 select-none"
                            :class="showFilters ? 'bg-blue-600 border-blue-600 text-white shadow-lg shadow-blue-600/20' : 'bg-slate-800 hover:bg-slate-700 text-slate-300'">
                        <i class="fas fa-sliders-h"></i>
                        <span>Filter & Urutkan</span>
                    </button>
                    <button type="submit" class="btn-premium py-2.5 px-5 text-sm flex items-center gap-2">
                        <i class="fas fa-search"></i>
                        <span>Cari</span>
                    </button>
                </div>
            </div>

            <!-- Expandable Advanced Filters (Category, Location, Sort) -->
            <div x-show="showFilters" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-3 border-t border-slate-800/60"
                 style="display: none;">
                
                <!-- Category Dropdown -->
                <div class="relative" x-data="{
                    open: false,
                    search: '',
                    options: [
                        { id: '', name: 'Semua Kategori' },
                        @foreach($categories as $cat)
                        { id: '{{ $cat->id }}', name: '{{ $cat->name }}' },
                        @endforeach
                    ],
                    get selectedLabel() {
                        let opt = this.options.find(o => o.id == selectedCategory);
                        return opt ? opt.name : 'Semua Kategori';
                    },
                    get filteredOptions() {
                        if (!this.search) return this.options;
                        return this.options.filter(o => o.name.toLowerCase().includes(this.search.toLowerCase()));
                    }
                }">
                    <input type="hidden" name="category" :value="selectedCategory">
                    <button type="button" @click="open = !open" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white text-sm flex items-center justify-between text-left focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all select-none">
                        <span class="truncate" x-text="selectedLabel">Semua Kategori</span>
                        <i class="fas fa-chevron-down text-slate-500 text-xs transition-transform" :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    
                    <!-- Dropdown List -->
                    <div x-show="open" @click.away="open = false" 
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute left-0 right-0 z-50 mt-2 bg-slate-900/95 border border-slate-700 rounded-xl shadow-2xl backdrop-blur-md overflow-hidden custom-dropdown-menu" style="display: none;">
                        
                        <!-- Search bar inside dropdown -->
                        <div class="p-2 border-b border-slate-800">
                            <div class="relative">
                                <input type="text" x-model="search" placeholder="Cari kategori..." class="w-full bg-slate-950 border border-slate-800 rounded-lg pl-8 pr-3 py-1.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 custom-dropdown-search dropdown-search-input">
                                <i class="fas fa-search text-slate-500 text-xs dropdown-search-icon"></i>
                            </div>
                        </div>
                        
                        <!-- Options List -->
                        <div class="max-h-60 overflow-y-auto py-1">
                            <template x-for="opt in filteredOptions" :key="opt.id">
                                <button type="button" @click="selectedCategory = opt.id; open = false; search = ''" 
                                        class="w-full px-4 py-2 text-left text-sm text-slate-300 hover:bg-blue-600 hover:text-white flex items-center justify-between transition-colors custom-dropdown-option">
                                    <span class="truncate" x-text="opt.name"></span>
                                    <i class="fas fa-check text-xs text-blue-400 custom-dropdown-option-selected" x-show="selectedCategory == opt.id"></i>
                                </button>
                            </template>
                            <div x-show="filteredOptions.length === 0" class="px-4 py-3 text-xs text-slate-500 text-center">
                                Tidak ada hasil
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Location Dropdown -->
                <div class="relative" x-data="{
                    open: false,
                    search: '',
                    locationMap: window.categoryLocationMap || {},
                    allLocations: window.allLocations || [],
                    get options() {
                        let list = [''];
                        if (selectedCategory && this.locationMap[selectedCategory]) {
                            list = list.concat(this.locationMap[selectedCategory]);
                        } else {
                            list = list.concat(this.allLocations);
                        }
                        return list.map(loc => ({
                            val: loc,
                            name: loc === '' ? 'Semua Lokasi' : loc
                        }));
                    },
                    get selectedLabel() {
                        let opt = this.options.find(o => o.val == selectedLocation);
                        return opt ? opt.name : 'Semua Lokasi';
                    },
                    get filteredOptions() {
                        if (!this.search) return this.options;
                        return this.options.filter(o => o.name.toLowerCase().includes(this.search.toLowerCase()));
                    }
                }" x-init="$watch('selectedCategory', val => {
                    let validLocs = val ? (locationMap[val] || []) : allLocations;
                    if (!validLocs.includes(selectedLocation)) {
                        selectedLocation = '';
                    }
                })">
                    <input type="hidden" name="location" :value="selectedLocation">
                    <button type="button" @click="open = !open" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white text-sm flex items-center justify-between text-left focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all select-none">
                        <span class="truncate" x-text="selectedLabel">Semua Lokasi</span>
                        <i class="fas fa-chevron-down text-slate-500 text-xs transition-transform" :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    
                    <!-- Dropdown List -->
                    <div x-show="open" @click.away="open = false" 
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute left-0 right-0 z-50 mt-2 bg-slate-900/95 border border-slate-700 rounded-xl shadow-2xl backdrop-blur-md overflow-hidden custom-dropdown-menu" style="display: none;">
                        
                        <!-- Search bar inside dropdown -->
                        <div class="p-2 border-b border-slate-800">
                            <div class="relative">
                                <input type="text" x-model="search" placeholder="Cari lokasi..." class="w-full bg-slate-950 border border-slate-800 rounded-lg pl-8 pr-3 py-1.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 custom-dropdown-search dropdown-search-input">
                                <i class="fas fa-search text-slate-500 text-xs dropdown-search-icon"></i>
                            </div>
                        </div>
                        
                        <!-- Options List -->
                        <div class="max-h-60 overflow-y-auto py-1">
                            <template x-for="opt in filteredOptions" :key="opt.val">
                                <button type="button" @click="selectedLocation = opt.val; open = false; search = ''" 
                                        class="w-full px-4 py-2 text-left text-sm text-slate-300 hover:bg-blue-600 hover:text-white flex items-center justify-between transition-colors custom-dropdown-option">
                                    <span class="truncate" x-text="opt.name"></span>
                                    <i class="fas fa-check text-xs text-blue-400 custom-dropdown-option-selected" x-show="selectedLocation == opt.val"></i>
                                </button>
                            </template>
                            <div x-show="filteredOptions.length === 0" class="px-4 py-3 text-xs text-slate-500 text-center">
                                Tidak ada hasil
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sort Dropdown -->
                <div class="relative" x-data="{
                    open: false,
                    options: [
                        { val: 'date_desc', name: 'Terbaru', icon: 'fa-clock text-blue-400' },
                        { val: 'date_asc', name: 'Terlama', icon: 'fa-history text-slate-400' },
                        { val: 'salary_desc', name: 'Gaji Tertinggi', icon: 'fa-arrow-trend-up text-emerald-400' },
                        { val: 'salary_asc', name: 'Gaji Terendah', icon: 'fa-arrow-trend-down text-rose-400' },
                        { val: 'company_asc', name: 'Perusahaan (A-Z)', icon: 'fa-sort-alpha-down text-sky-400' },
                        { val: 'company_desc', name: 'Perusahaan (Z-A)', icon: 'fa-sort-alpha-down-alt text-sky-400' }
                    ],
                    get selectedLabel() {
                        let opt = this.options.find(o => o.val == selectedSort);
                        return opt ? opt.name : 'Terbaru';
                    },
                    get selectedIcon() {
                        let opt = this.options.find(o => o.val == selectedSort);
                        return opt ? opt.icon : 'fa-clock text-blue-400';
                    }
                }">
                    <input type="hidden" name="sort" :value="selectedSort">
                    <button type="button" @click="open = !open" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white text-sm flex items-center justify-between text-left focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all select-none">
                        <span class="truncate flex items-center gap-2">
                            <i :class="'fas ' + selectedIcon"></i>
                            <span x-text="selectedLabel">Terbaru</span>
                        </span>
                        <i class="fas fa-chevron-down text-slate-500 text-xs transition-transform" :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    
                    <!-- Dropdown List -->
                    <div x-show="open" @click.away="open = false" 
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute left-0 right-0 z-50 mt-2 bg-slate-900/95 border border-slate-700 rounded-xl shadow-2xl backdrop-blur-md overflow-hidden custom-dropdown-menu" style="display: none;">
                        
                        <!-- Options List -->
                        <div class="py-1">
                            <template x-for="opt in options" :key="opt.val">
                                <button type="button" @click="selectedSort = opt.val; open = false" 
                                        class="w-full px-4 py-2 text-left text-sm text-slate-300 hover:bg-blue-600 hover:text-white flex items-center justify-between transition-colors custom-dropdown-option">
                                    <span class="truncate flex items-center gap-2">
                                        <i :class="'fas ' + opt.icon" class="w-4 text-center"></i>
                                        <span x-text="opt.name"></span>
                                    </span>
                                    <i class="fas fa-check text-xs text-blue-400 custom-dropdown-option-selected" x-show="selectedSort == opt.val"></i>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Job Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($jobs as $job)
        <div class="glass-dark p-6 card-hover flex flex-col h-full animate-fade-in" style="animation-delay: {{ $loop->index % 6 * 50 }}ms">
            <div class="flex items-start justify-between mb-6">
                <div class="w-12 h-12 bg-slate-800 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-building text-slate-500 text-xl"></i>
                </div>
                <span class="px-3 py-1 rounded-full bg-blue-500/10 text-blue-400 text-[10px] font-bold uppercase tracking-widest">{{ $job->type }}</span>
            </div>

            <div class="flex-1">
                <h3 class="text-xl font-bold text-white mb-1">{{ $job->title }}</h3>
                <p class="text-sm text-blue-500 mb-4">{{ $job->company_name }}</p>
                <p class="text-slate-400 text-sm line-clamp-2 mb-6">{{ $job->description }}</p>
            </div>

            <div class="space-y-4 pt-6 border-t border-slate-800">
                <div class="flex items-center justify-between text-xs font-medium">
                    <span class="text-slate-500"><i class="fas fa-map-marker-alt mr-1"></i> {{ $job->location }}</span>
                    <span class="text-emerald-500 font-bold">{{ $job->salary_range }}</span>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('jobs.show', $job->slug) }}" class="flex-1 py-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-center text-sm font-bold transition-all">Lihat Detail</a>
                    <form action="{{ route('tracker.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="job_id" value="{{ $job->id }}">
                        <button type="submit" class="w-11 h-11 rounded-xl bg-slate-800 hover:bg-blue-600 text-slate-400 hover:text-white transition-all flex items-center justify-center" title="Simpan ke Pelacak">
                            <i class="fas fa-bookmark"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="mt-12">
        {{ $jobs->links() }}
    </div>
</div>

@push('scripts')
<script>
    window.categoryLocationMap = @json($categoryLocationMap);
    window.allLocations = @json($allLocations);
</script>
<style>
    /* Custom scrollbar for dropdown menus */
    .max-h-60::-webkit-scrollbar {
        width: 6px;
    }
    .max-h-60::-webkit-scrollbar-track {
        background: rgba(15, 23, 42, 0.5);
    }
    .max-h-60::-webkit-scrollbar-thumb {
        background-color: #334155;
        border-radius: 999px;
    }
    .max-h-60::-webkit-scrollbar-thumb:hover {
        background-color: #475569;
    }

    /* Light mode scrollbar and dropdown elements overrides */
    .light-mode .max-h-60::-webkit-scrollbar-track {
        background: rgba(241, 245, 249, 0.5);
    }
    .light-mode .max-h-60::-webkit-scrollbar-thumb {
        background-color: #cbd5e1;
    }
    .light-mode .max-h-60::-webkit-scrollbar-thumb:hover {
        background-color: #94a3b8;
    }

    .light-mode .custom-dropdown-menu {
        background-color: #ffffff !important;
        border-color: #e2e8f0 !important;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08) !important;
    }
    .light-mode .custom-dropdown-search {
        background-color: #f8fafc !important;
        border-color: #e2e8f0 !important;
        color: #0f172a !important;
    }
    .light-mode .custom-dropdown-option {
        color: #334155 !important;
    }
    .light-mode .custom-dropdown-option:hover {
        background-color: #3b82f6 !important;
        color: #ffffff !important;
    }
    .light-mode .custom-dropdown-option-selected {
        color: #2563eb !important;
    }
    .light-mode .custom-dropdown-option:hover .custom-dropdown-option-selected {
        color: #ffffff !important;
    }

    /* Bulletproof Search Icon positioning */
    .search-icon {
        position: absolute !important;
        left: 14px !important;
        top: 50% !important;
        transform: translateY(-50%) !important;
        pointer-events: none !important;
        z-index: 10 !important;
    }
    .search-input {
        padding-left: 38px !important;
    }

    .dropdown-search-icon {
        position: absolute !important;
        left: 10px !important;
        top: 50% !important;
        transform: translateY(-50%) !important;
        pointer-events: none !important;
        z-index: 10 !important;
    }
    .dropdown-search-input {
        padding-left: 30px !important;
    }
</style>
@endpush
@endsection
