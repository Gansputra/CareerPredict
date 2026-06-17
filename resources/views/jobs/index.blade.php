@extends('layouts.app')

@section('title', 'Jelajahi Lowongan')

@section('content')
<script>
    // Define jobSearch before Alpine evaluates x-data
    window._jobsConfig = {
        searchUrl: '{{ route('jobs.search') }}',
        initialSearch: @json(request('search', '')),
        initialCategory: @json(request('category', '')),
        initialLocation: @json(request('location', '')),
        initialSort: @json(request('sort', 'date_desc')),
        initialType: @json(request('type', '')),
        categoryLocationMap: @json($categoryLocationMap),
        allLocations: @json($allLocations),
        categories: @json($categories->map(fn($c) => ['id' => (string)$c->id, 'name' => $c->name])->values()),
    };
    document.addEventListener('alpine:init', function () {
        Alpine.data('jobSearch', function () {
            const cfg = window._jobsConfig;
            return {
                searchUrl: cfg.searchUrl,
                searchInput: cfg.initialSearch,
                selectedCategory: cfg.initialCategory,
                selectedLocation: cfg.initialLocation,
                selectedSort: cfg.initialSort,
                selectedType: cfg.initialType,
                categoryLocationMap: cfg.categoryLocationMap,
                allLocations: cfg.allLocations,
                categoryOptions: cfg.categories,
                showFilters: cfg.initialCategory !== '' || cfg.initialLocation !== '' || (cfg.initialSort !== 'date_desc' && cfg.initialSort !== ''),

                jobs: [],
                total: 0,
                currentPage: 1,
                lastPage: 1,
                loading: true,

                init() {
                    this.fetchJobs();
                },

                fetchJobs(page = 1) {
                    this.loading = true;
                    this.currentPage = page;

                    const params = new URLSearchParams();
                    if (this.searchInput)      params.set('search',   this.searchInput);
                    if (this.selectedCategory) params.set('category', this.selectedCategory);
                    if (this.selectedLocation) params.set('location', this.selectedLocation);
                    if (this.selectedSort && this.selectedSort !== 'date_desc') params.set('sort', this.selectedSort);
                    if (this.selectedType)     params.set('type',     this.selectedType);
                    params.set('page', page);

                    fetch(`${this.searchUrl}?${params.toString()}`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                    })
                    .then(r => r.json())
                    .then(data => {
                        this.jobs     = data.jobs;
                        this.total    = data.total;
                        this.lastPage = data.last_page;
                        this.currentPage = data.page;
                        this.loading  = false;
                    })
                    .catch(() => { this.loading = false; });
                },

                goToPage(p) {
                    if (p < 1 || p > this.lastPage) return;
                    this.fetchJobs(p);
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                },

                get pageRange() {
                    const total = this.lastPage, cur = this.currentPage;
                    if (total <= 7) return Array.from({ length: total }, (_, i) => i + 1);
                    if (cur <= 4)           return [1, 2, 3, 4, 5, '…', total];
                    if (cur >= total - 3)   return [1, '…', total-4, total-3, total-2, total-1, total];
                    return [1, '…', cur-1, cur, cur+1, '…', total];
                },

                applySmartChip(params) {
                    if (params.category  !== undefined) this.selectedCategory = String(params.category);
                    if (params.search    !== undefined) this.searchInput       = params.search;
                    if (params.sort      !== undefined) this.selectedSort      = params.sort;
                    if (params.type      !== undefined) this.selectedType      = params.type;
                    this.fetchJobs();
                },

                isSmartChipActive(params) {
                    return Object.entries(params).every(([k, v]) => {
                        if (k === 'category') return String(this.selectedCategory) === String(v);
                        if (k === 'search')   return this.searchInput === v;
                        if (k === 'sort')     return this.selectedSort === v;
                        if (k === 'type')     return this.selectedType === v;
                        return false;
                    });
                },

                hasActiveFilters() {
                    return this.searchInput !== '' || this.selectedCategory !== '' ||
                           this.selectedLocation !== '' || this.selectedSort !== 'date_desc' ||
                           this.selectedType !== '';
                },

                resetFilters() {
                    this.searchInput = ''; this.selectedCategory = '';
                    this.selectedLocation = ''; this.selectedSort = 'date_desc';
                    this.selectedType = '';
                    this.fetchJobs();
                },

                saveToTracker(jobId) {
                    const data = new FormData();
                    data.append('_token', document.querySelector('meta[name="csrf-token"]').content);
                    data.append('job_id', jobId);
                    fetch('{{ route('tracker.store') }}', { method: 'POST', body: data })
                    .then(r => {
                        if (r.ok || r.redirected) {
                            Swal.fire({ icon: 'success', title: 'Disimpan!', text: 'Ditambahkan ke Pelacak Lamaran.', timer: 1800, showConfirmButton: false, background: '#1e293b', color: '#fff' });
                        }
                    });
                }
            };
        });
    });
</script>

<div class="space-y-6"
     x-data="jobSearch"
     x-init="init()">

    {{-- ============================================================ --}}
    {{-- SMART AI FILTER CHIPS                                         --}}
    {{-- ============================================================ --}}
    @if(!empty($smartSuggestions))
    <div x-data="{ visible: false }" x-init="setTimeout(() => visible = true, 100)">
        <div x-show="visible"
             x-transition:enter="transition ease-out duration-500"
             x-transition:enter-start="opacity-0 -translate-y-3"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="glass p-4 sm:p-5 border border-blue-500/20 relative overflow-hidden">

            <div class="absolute inset-0 pointer-events-none">
                <div class="absolute -top-8 -left-8 w-48 h-48 bg-blue-600/10 rounded-full blur-2xl animate-pulse"></div>
                <div class="absolute -bottom-6 -right-6 w-40 h-40 bg-violet-600/10 rounded-full blur-2xl animate-pulse" style="animation-delay:1s"></div>
            </div>

            <div class="relative flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-4">
                <div class="flex items-center gap-2 shrink-0">
                    <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-blue-500 to-violet-500 flex items-center justify-center shadow-lg shadow-blue-500/30">
                        <i class="fas fa-wand-magic-sparkles text-white text-[11px]"></i>
                    </div>
                    <span class="text-xs font-bold text-slate-300 whitespace-nowrap">Rekomendasi<br class="hidden sm:block"> untukmu</span>
                </div>

                <div class="hidden sm:block w-px h-8 bg-slate-700/60 shrink-0"></div>

                <div class="flex flex-wrap gap-2">
                    @foreach($smartSuggestions as $i => $chip)
                    @php
                        $colorMap = [
                            'amber'   => ['ring'=>'ring-amber-500/40',   'bg'=>'bg-amber-500/10',   'text'=>'text-amber-400',   'hover'=>'hover:bg-amber-500/20 hover:ring-amber-400/60',   'active'=>'bg-amber-500/25 ring-amber-400/80 shadow-amber-500/20'],
                            'blue'    => ['ring'=>'ring-blue-500/40',    'bg'=>'bg-blue-500/10',    'text'=>'text-blue-400',    'hover'=>'hover:bg-blue-500/20 hover:ring-blue-400/60',    'active'=>'bg-blue-500/25 ring-blue-400/80 shadow-blue-500/20'],
                            'violet'  => ['ring'=>'ring-violet-500/40',  'bg'=>'bg-violet-500/10',  'text'=>'text-violet-400',  'hover'=>'hover:bg-violet-500/20 hover:ring-violet-400/60',  'active'=>'bg-violet-500/25 ring-violet-400/80 shadow-violet-500/20'],
                            'emerald' => ['ring'=>'ring-emerald-500/40', 'bg'=>'bg-emerald-500/10', 'text'=>'text-emerald-400', 'hover'=>'hover:bg-emerald-500/20 hover:ring-emerald-400/60', 'active'=>'bg-emerald-500/25 ring-emerald-400/80 shadow-emerald-500/20'],
                        ];
                        $c = $colorMap[$chip['color']] ?? $colorMap['blue'];
                        $chipParams = $chip['params'];
                    @endphp
                    <button type="button"
                            style="animation-delay: {{ $i * 60 }}ms"
                            title="{{ $chip['tooltip'] }}"
                            @click="applySmartChip({{ json_encode($chipParams) }})"
                            :class="isSmartChipActive({{ json_encode($chipParams) }}) ? 'ring-2 shadow-md {{ $c['active'] }}' : '{{ $c['ring'] }} {{ $c['bg'] }} {{ $c['hover'] }}'"
                            class="smart-chip inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold ring-1 transition-all duration-200 cursor-pointer select-none {{ $c['text'] }}">
                        <i class="fas {{ $chip['icon'] }} text-[10px]"></i>
                        <span>{{ $chip['label'] }}</span>
                        <i class="fas fa-check text-[9px] opacity-80" x-show="isSmartChipActive({{ json_encode($chipParams) }})"></i>
                    </button>
                    @endforeach

                    {{-- Reset chip --}}
                    <button type="button"
                            x-show="hasActiveFilters()"
                            @click="resetFilters()"
                            x-transition
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold ring-1 ring-slate-600/40 bg-slate-700/30 text-slate-400 hover:bg-slate-700/60 hover:text-slate-200 transition-all duration-200">
                        <i class="fas fa-xmark text-[10px]"></i>
                        <span>Reset Filter</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ============================================================ --}}
    {{-- FILTER SECTION                                                --}}
    {{-- ============================================================ --}}
    <div class="glass p-4 relative z-40" style="overflow:visible;">
        <div class="space-y-4">
            {{-- Main Search Bar Row --}}
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <input type="text"
                           x-model="searchInput"
                           @input.debounce.400ms="fetchJobs()"
                           placeholder="Cari berdasarkan judul, perusahaan, atau kata kunci..."
                           class="w-full bg-slate-900 border border-slate-700 rounded-xl pl-10 pr-4 py-2.5 text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all search-input">
                    <i class="fas fa-search text-slate-500 text-sm search-icon"></i>
                </div>
                <div class="flex gap-2">
                    <button type="button" @click="showFilters = !showFilters"
                            class="px-4 py-2.5 rounded-xl border text-sm font-semibold transition-all flex items-center gap-2 select-none"
                            :class="showFilters ? 'bg-blue-600 border-blue-600 text-white shadow-lg shadow-blue-600/20' : 'bg-slate-800 border-slate-700 hover:bg-slate-700 text-slate-300'">
                        <i class="fas fa-sliders-h"></i>
                        <span>Filter & Urutkan</span>
                        {{-- Active filter dot --}}
                        <span x-show="selectedCategory !== '' || selectedLocation !== '' || selectedSort !== 'date_desc'"
                              class="w-2 h-2 rounded-full bg-blue-400 animate-pulse"></span>
                    </button>
                </div>
            </div>

            {{-- Work Method Filter (always visible) --}}
            <div class="flex flex-wrap gap-2 pt-1">
                <span class="text-[10px] font-bold uppercase tracking-widest text-slate-500 self-center mr-1">Metode Kerja:</span>
                @php
                    $workTypes = [
                        ['val' => '',                    'label' => 'Semua',       'icon' => 'fa-border-all'],
                        ['val' => 'Penuh Waktu',         'label' => 'Penuh Waktu', 'icon' => 'fa-briefcase'],
                        ['val' => 'Paruh Waktu',         'label' => 'Paruh Waktu', 'icon' => 'fa-clock'],
                        ['val' => 'Kontrak',             'label' => 'Kontrak',     'icon' => 'fa-file-signature'],
                        ['val' => 'Jarak Jauh (Remote)', 'label' => 'Remote',      'icon' => 'fa-laptop-house'],
                    ];
                @endphp
                @foreach($workTypes as $wt)
                <button type="button"
                        @click="selectedType = '{{ $wt['val'] }}'; fetchJobs()"
                        :class="selectedType === '{{ $wt['val'] }}'
                            ? 'bg-blue-600 text-white border-blue-500 shadow-lg shadow-blue-600/20'
                            : 'bg-slate-800/60 text-slate-400 border-slate-700 hover:bg-slate-700 hover:text-slate-200'"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border text-xs font-semibold transition-all duration-200 select-none">
                    <i class="fas {{ $wt['icon'] }} text-[10px]"></i>
                    <span>{{ $wt['label'] }}</span>
                </button>
                @endforeach
            </div>

            {{-- Expandable Advanced Filters --}}
            <div x-show="showFilters"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-3 border-t border-slate-800/60"
                 style="display: none; overflow: visible;">

                {{-- Category Dropdown --}}
                <div class="relative" x-data="{
                    open: false,
                    search: '',
                    get options() {
                        return [{ id: '', name: 'Semua Kategori' }].concat(categoryOptions);
                    },
                    get selectedLabel() {
                        let opt = this.options.find(o => o.id == selectedCategory);
                        return opt ? opt.name : 'Semua Kategori';
                    },
                    get filteredOptions() {
                        if (!this.search) return this.options;
                        return this.options.filter(o => o.name.toLowerCase().includes(this.search.toLowerCase()));
                    }
                }">
                    <button type="button" @click="open = !open" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white text-sm flex items-center justify-between text-left focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all select-none">
                        <span class="truncate" x-text="selectedLabel">Semua Kategori</span>
                        <i class="fas fa-chevron-down text-slate-500 text-xs transition-transform" :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open" @click.away="open = false"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute left-0 right-0 z-50 mt-2 bg-slate-900/95 border border-slate-700 rounded-xl shadow-2xl backdrop-blur-md overflow-hidden custom-dropdown-menu" style="display:none;">
                        <div class="p-2 border-b border-slate-800">
                            <div class="relative">
                                <input type="text" x-model="search" placeholder="Cari kategori..." class="w-full bg-slate-950 border border-slate-800 rounded-lg pl-8 pr-3 py-1.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 custom-dropdown-search dropdown-search-input">
                                <i class="fas fa-search text-slate-500 text-xs dropdown-search-icon"></i>
                            </div>
                        </div>
                        <div class="max-h-60 overflow-y-auto py-1">
                            <template x-for="opt in filteredOptions" :key="opt.id">
                                <button type="button"
                                        @click="selectedCategory = opt.id; selectedLocation = ''; fetchJobs(); open = false; search = ''"
                                        class="w-full px-4 py-2 text-left text-sm text-slate-300 hover:bg-blue-600 hover:text-white flex items-center justify-between transition-colors custom-dropdown-option">
                                    <span class="truncate" x-text="opt.name"></span>
                                    <i class="fas fa-check text-xs text-blue-400 custom-dropdown-option-selected" x-show="selectedCategory == opt.id"></i>
                                </button>
                            </template>
                            <div x-show="filteredOptions.length === 0" class="px-4 py-3 text-xs text-slate-500 text-center">Tidak ada hasil</div>
                        </div>
                    </div>
                </div>

                {{-- Location Dropdown --}}
                <div class="relative" x-data="{
                    open: false,
                    search: '',
                    get locationOptions() {
                        let map = categoryLocationMap;
                        let all = allLocations;
                        let cat = selectedCategory;
                        let list = cat && map[cat] ? map[cat] : all;
                        return [{ val: '', name: 'Semua Lokasi' }].concat(list.map(l => ({ val: l, name: l })));
                    },
                    get selectedLabel() {
                        let opt = this.locationOptions.find(o => o.val == selectedLocation);
                        return opt ? opt.name : 'Semua Lokasi';
                    },
                    get filteredOptions() {
                        if (!this.search) return this.locationOptions;
                        return this.locationOptions.filter(o => o.name.toLowerCase().includes(this.search.toLowerCase()));
                    }
                }">
                    <button type="button" @click="open = !open" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white text-sm flex items-center justify-between text-left focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all select-none">
                        <span class="truncate" x-text="selectedLabel">Semua Lokasi</span>
                        <i class="fas fa-chevron-down text-slate-500 text-xs transition-transform" :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open" @click.away="open = false"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute left-0 right-0 z-50 mt-2 bg-slate-900/95 border border-slate-700 rounded-xl shadow-2xl backdrop-blur-md overflow-hidden custom-dropdown-menu" style="display:none;">
                        <div class="p-2 border-b border-slate-800">
                            <div class="relative">
                                <input type="text" x-model="search" placeholder="Cari lokasi..." class="w-full bg-slate-950 border border-slate-800 rounded-lg pl-8 pr-3 py-1.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 custom-dropdown-search dropdown-search-input">
                                <i class="fas fa-search text-slate-500 text-xs dropdown-search-icon"></i>
                            </div>
                        </div>
                        <div class="max-h-60 overflow-y-auto py-1">
                            <template x-for="opt in filteredOptions" :key="opt.val">
                                <button type="button"
                                        @click="selectedLocation = opt.val; fetchJobs(); open = false; search = ''"
                                        class="w-full px-4 py-2 text-left text-sm text-slate-300 hover:bg-blue-600 hover:text-white flex items-center justify-between transition-colors custom-dropdown-option">
                                    <span class="truncate" x-text="opt.name"></span>
                                    <i class="fas fa-check text-xs text-blue-400 custom-dropdown-option-selected" x-show="selectedLocation == opt.val"></i>
                                </button>
                            </template>
                            <div x-show="filteredOptions.length === 0" class="px-4 py-3 text-xs text-slate-500 text-center">Tidak ada hasil</div>
                        </div>
                    </div>
                </div>

                {{-- Sort Dropdown --}}
                <div class="relative" x-data="{
                    open: false,
                    options: [
                        { val: 'date_desc',    name: 'Terbaru',          icon: 'fa-clock text-blue-400' },
                        { val: 'date_asc',     name: 'Terlama',          icon: 'fa-history text-slate-400' },
                        { val: 'salary_desc',  name: 'Gaji Tertinggi',   icon: 'fa-arrow-trend-up text-emerald-400' },
                        { val: 'salary_asc',   name: 'Gaji Terendah',    icon: 'fa-arrow-trend-down text-rose-400' },
                        { val: 'company_asc',  name: 'Perusahaan (A-Z)', icon: 'fa-sort-alpha-down text-sky-400' },
                        { val: 'company_desc', name: 'Perusahaan (Z-A)', icon: 'fa-sort-alpha-down-alt text-sky-400' },
                    ],
                    get selectedLabel() {
                        return (this.options.find(o => o.val == selectedSort) ?? this.options[0]).name;
                    },
                    get selectedIcon() {
                        return (this.options.find(o => o.val == selectedSort) ?? this.options[0]).icon;
                    }
                }">
                    <button type="button" @click="open = !open" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white text-sm flex items-center justify-between text-left focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all select-none">
                        <span class="truncate flex items-center gap-2">
                            <i :class="'fas ' + selectedIcon"></i>
                            <span x-text="selectedLabel">Terbaru</span>
                        </span>
                        <i class="fas fa-chevron-down text-slate-500 text-xs transition-transform" :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open" @click.away="open = false"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute left-0 right-0 z-50 mt-2 bg-slate-900/95 border border-slate-700 rounded-xl shadow-2xl backdrop-blur-md overflow-hidden custom-dropdown-menu" style="display:none;">
                        <div class="py-1">
                            <template x-for="opt in options" :key="opt.val">
                                <button type="button"
                                        @click="selectedSort = opt.val; fetchJobs(); open = false"
                                        class="w-full px-4 py-2 text-left text-sm text-slate-300 hover:bg-blue-600 hover:text-white flex items-center justify-between transition-colors custom-dropdown-option">
                                    <span class="flex items-center gap-2">
                                        <i :class="'fas w-4 text-center ' + opt.icon"></i>
                                        <span x-text="opt.name"></span>
                                    </span>
                                    <i class="fas fa-check text-xs text-blue-400 custom-dropdown-option-selected" x-show="selectedSort == opt.val"></i>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- RESULT COUNT & ACTIVE FILTERS SUMMARY                        --}}
    {{-- ============================================================ --}}
    <div class="flex items-center justify-between text-sm">
        <div class="flex items-center gap-2 text-slate-400">
            <template x-if="loading">
                <span class="flex items-center gap-2">
                    <svg class="animate-spin w-4 h-4 text-blue-400" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                    </svg>
                    <span class="text-slate-500">Memuat...</span>
                </span>
            </template>
            <template x-if="!loading">
                <span>
                    Menampilkan <span class="font-bold text-white" x-text="jobs.length"></span> dari
                    <span class="font-bold text-white" x-text="total"></span> lowongan
                </span>
            </template>
        </div>
        {{-- Active type badge --}}
        <span x-show="selectedType !== ''" class="text-xs font-semibold px-2 py-1 rounded-full bg-blue-500/15 text-blue-400 border border-blue-500/30 flex items-center gap-1">
            <i class="fas fa-tag text-[9px]"></i>
            <span x-text="selectedType"></span>
            <button @click="selectedType = ''; fetchJobs()" class="ml-1 hover:text-white transition-colors"><i class="fas fa-xmark text-[9px]"></i></button>
        </span>
    </div>

    {{-- ============================================================ --}}
    {{-- JOB GRID                                                      --}}
    {{-- ============================================================ --}}

    {{-- Skeleton Loading --}}
    <div x-show="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <template x-for="i in 6" :key="i">
            <div class="glass-dark p-6 animate-pulse">
                <div class="flex items-start justify-between mb-6">
                    <div class="w-12 h-12 bg-slate-700/50 rounded-2xl"></div>
                    <div class="w-20 h-6 bg-slate-700/50 rounded-full"></div>
                </div>
                <div class="space-y-2 mb-4">
                    <div class="h-5 bg-slate-700/50 rounded w-3/4"></div>
                    <div class="h-3 bg-slate-700/30 rounded w-1/2"></div>
                </div>
                <div class="h-8 bg-slate-700/20 rounded w-full mb-6"></div>
                <div class="pt-4 border-t border-slate-800 space-y-3">
                    <div class="h-3 bg-slate-700/30 rounded w-full"></div>
                    <div class="h-10 bg-slate-700/30 rounded-xl"></div>
                </div>
            </div>
        </template>
    </div>

    {{-- Actual Job Cards --}}
    <div x-show="!loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <template x-if="jobs.length === 0 && !loading">
            <div class="col-span-3 text-center py-20">
                <div class="w-16 h-16 rounded-full bg-slate-800 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-search text-slate-500 text-2xl"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-400 mb-1">Lowongan tidak ditemukan</h3>
                <p class="text-sm text-slate-500">Coba ubah filter atau kata kunci pencarianmu</p>
                <button @click="resetFilters()" class="mt-4 px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold transition-all">Reset Filter</button>
            </div>
        </template>
        <template x-for="(job, index) in jobs" :key="job.id">
            <div class="glass-dark p-6 card-hover flex flex-col h-full job-card-enter" :style="`animation-delay: ${(index % 6) * 50}ms`">
                <div class="flex items-start justify-between mb-6">
                    <div class="w-12 h-12 bg-slate-800 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-building text-slate-500 text-xl"></i>
                    </div>
                    <span class="px-3 py-1 rounded-full bg-blue-500/10 text-blue-400 text-[10px] font-bold uppercase tracking-widest" x-text="job.type"></span>
                </div>

                <div class="flex-1">
                    <h3 class="text-xl font-bold text-white mb-1" x-text="job.title"></h3>
                    <p class="text-sm text-blue-500 mb-4" x-text="job.company_name"></p>
                    <p class="text-slate-400 text-sm line-clamp-2 mb-6" x-text="job.description"></p>
                </div>

                <div class="space-y-4 pt-6 border-t border-slate-800">
                    <div class="flex items-center justify-between text-xs font-medium">
                        <span class="text-slate-500"><i class="fas fa-map-marker-alt mr-1"></i> <span x-text="job.location"></span></span>
                        <span class="text-emerald-500 font-bold" x-text="job.salary_range || 'Negosiasi'"></span>
                    </div>
                    <div class="flex gap-2">
                        <a :href="`/jobs/${job.slug}`" class="flex-1 py-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-center text-sm font-bold transition-all">Lihat Detail</a>
                        <form :action="`{{ route('tracker.store') }}`" method="POST" @submit.prevent="saveToTracker(job.id, $el)">
                            @csrf
                            <button type="submit" class="w-11 h-11 rounded-xl bg-slate-800 hover:bg-blue-600 text-slate-400 hover:text-white transition-all flex items-center justify-center" title="Simpan ke Pelacak">
                                <i class="fas fa-bookmark"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </template>
    </div>

    {{-- ============================================================ --}}
    {{-- AJAX PAGINATION                                               --}}
    {{-- ============================================================ --}}
    <div x-show="lastPage > 1 && !loading" class="flex items-center justify-center gap-2 mt-8 flex-wrap">
        <button @click="goToPage(currentPage - 1)" :disabled="currentPage === 1"
                class="px-4 py-2 rounded-xl text-sm font-semibold transition-all border"
                :class="currentPage === 1 ? 'opacity-30 cursor-not-allowed bg-slate-800 border-slate-700 text-slate-500' : 'bg-slate-800 border-slate-700 text-white hover:bg-slate-700'">
            <i class="fas fa-chevron-left text-xs"></i>
        </button>

        <template x-for="p in pageRange" :key="p">
            <button @click="p !== '…' && goToPage(p)"
                    :class="p === currentPage ? 'bg-blue-600 text-white border-blue-500 shadow-lg shadow-blue-600/20' : p === '…' ? 'cursor-default text-slate-500 border-transparent bg-transparent' : 'bg-slate-800 border-slate-700 text-white hover:bg-slate-700'"
                    class="w-10 h-10 rounded-xl text-sm font-semibold transition-all border flex items-center justify-center"
                    x-text="p">
            </button>
        </template>

        <button @click="goToPage(currentPage + 1)" :disabled="currentPage === lastPage"
                class="px-4 py-2 rounded-xl text-sm font-semibold transition-all border"
                :class="currentPage === lastPage ? 'opacity-30 cursor-not-allowed bg-slate-800 border-slate-700 text-slate-500' : 'bg-slate-800 border-slate-700 text-white hover:bg-slate-700'">
            <i class="fas fa-chevron-right text-xs"></i>
        </button>
    </div>

</div>
@endsection

@push('scripts')
<style>
    /* Smart chip entrance animation */
    @keyframes chipPop {
        0%   { opacity: 0; transform: scale(0.8) translateY(4px); }
        70%  { transform: scale(1.05) translateY(-1px); }
        100% { opacity: 1; transform: scale(1) translateY(0); }
    }
    .smart-chip {
        animation: chipPop 0.35s ease forwards;
        opacity: 0;
    }
    .smart-chip:hover { transform: translateY(-1px) scale(1.04); }
    .smart-chip:active { transform: translateY(0) scale(0.97); }

    /* Job card entrance */
    @keyframes cardIn {
        from { opacity: 0; transform: translateY(12px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .job-card-enter {
        animation: cardIn 0.3s ease forwards;
        opacity: 0;
    }

    /* Custom scrollbar */
    .max-h-60::-webkit-scrollbar { width: 6px; }
    .max-h-60::-webkit-scrollbar-track { background: rgba(15,23,42,.5); }
    .max-h-60::-webkit-scrollbar-thumb { background-color: #334155; border-radius: 999px; }
    .max-h-60::-webkit-scrollbar-thumb:hover { background-color: #475569; }

    /* Light mode */
    .light-mode .max-h-60::-webkit-scrollbar-track { background: rgba(241,245,249,.5); }
    .light-mode .max-h-60::-webkit-scrollbar-thumb { background-color: #cbd5e1; }
    .light-mode .custom-dropdown-menu { background-color:#fff!important; border-color:#e2e8f0!important; box-shadow:0 10px 25px rgba(0,0,0,.08)!important; }
    .light-mode .custom-dropdown-search { background-color:#f8fafc!important; border-color:#e2e8f0!important; color:#0f172a!important; }
    .light-mode .custom-dropdown-option { color:#334155!important; }
    .light-mode .custom-dropdown-option:hover { background-color:#3b82f6!important; color:#fff!important; }
    .light-mode .custom-dropdown-option-selected { color:#2563eb!important; }
    .light-mode .custom-dropdown-option:hover .custom-dropdown-option-selected { color:#fff!important; }

    /* Search icon positioning */
    .search-icon { position:absolute!important; left:14px!important; top:50%!important; transform:translateY(-50%)!important; pointer-events:none!important; z-index:10!important; }
    .search-input { padding-left:38px!important; }
    .dropdown-search-icon { position:absolute!important; left:10px!important; top:50%!important; transform:translateY(-50%)!important; pointer-events:none!important; z-index:10!important; }
    .dropdown-search-input { padding-left:30px!important; }
</style>
@endpush
