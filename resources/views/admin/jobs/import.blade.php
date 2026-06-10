@extends('layouts.app')

@section('title', 'Impor Data Lowongan')

@section('content')
<div class="max-w-5xl mx-auto space-y-8">

    {{-- Header --}}
    <div class="flex justify-between items-start animate-fade-in">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-500/10 text-purple-400 text-xs font-bold tracking-widest uppercase mb-3 border border-purple-500/20">
                <i class="fas fa-database"></i>
                Manajemen Data
            </div>
            <h1 class="text-3xl font-extrabold text-white mb-1">Impor <span class="text-gradient">Lowongan</span></h1>
            <p class="text-slate-400 text-sm">Pilih sumber data: ambil dari API publik atau unggah dataset CSV.</p>
        </div>
        <form action="{{ route('admin.jobs.clear') }}" method="POST" onsubmit="return confirm('⚠️ Apakah Anda yakin ingin menghapus SEMUA lowongan? Tindakan ini tidak dapat dibatalkan.');">
            @csrf
            <button type="submit" class="px-4 py-2 rounded-xl bg-red-500/10 text-red-500 border border-red-500/20 hover:bg-red-500 hover:text-white transition-all text-sm font-bold flex items-center gap-2">
                <i class="fas fa-trash"></i> Hapus Semua Lowongan
            </button>
        </form>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="glass-dark p-5 text-center animate-fade-in">
            <p class="text-2xl font-extrabold text-white">{{ $stats['total_jobs'] }}</p>
            <p class="text-[10px] uppercase tracking-widest text-slate-500 mt-1">Total Lowongan</p>
        </div>
        <div class="glass-dark p-5 text-center animate-fade-in" style="animation-delay: 60ms">
            <p class="text-2xl font-extrabold text-emerald-400">{{ $stats['active_jobs'] }}</p>
            <p class="text-[10px] uppercase tracking-widest text-slate-500 mt-1">Aktif</p>
        </div>
        <div class="glass-dark p-5 text-center animate-fade-in" style="animation-delay: 120ms">
            <p class="text-2xl font-extrabold text-blue-400">{{ $stats['categories'] }}</p>
            <p class="text-[10px] uppercase tracking-widest text-slate-500 mt-1">Kategori</p>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-emerald-500/10 border border-emerald-500 text-emerald-400 p-4 rounded-xl flex items-center gap-3 animate-fade-in">
        <i class="fas fa-check-circle text-lg"></i>
        <p class="font-medium text-sm">{{ session('success') }}</p>
    </div>
    @endif

    @if($errors->any())
    <div class="bg-red-500/10 border border-red-500 text-red-400 p-4 rounded-xl animate-fade-in">
        @foreach($errors->all() as $error)
        <p class="text-sm"><i class="fas fa-times-circle mr-2"></i>{{ $error }}</p>
        @endforeach
    </div>
    @endif

    {{-- API Fetch Section --}}
    <div class="glass p-8 animate-fade-in" style="animation-delay: 200ms">
        <div class="flex items-center gap-4 mb-8 pb-6 border-b border-slate-800">
            <div class="w-14 h-14 bg-blue-600/10 rounded-2xl flex items-center justify-center">
                <i class="fas fa-cloud-arrow-down text-blue-400 text-2xl"></i>
            </div>
            <div>
                <h2 class="text-xl font-bold text-white">Ambil dari API Publik</h2>
                <p class="text-slate-400 text-xs">Ambil lowongan kerja nyata dari Remotive, Arbeitnow & JSearch</p>
            </div>
        </div>

        <form action="{{ route('admin.jobs.import.api') }}" method="POST" x-data="{ loading: false }" @submit="loading = true">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                {{-- Source --}}
                <div>
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2 block">Sumber</label>
                    <select name="source" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-white text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all">
                        <option value="all">🌐 Semua Sumber</option>
                        <option value="jsearch">🔴 JSearch (Pekerjaan New York)</option>
                        <option value="remotive">🟢 Remotive (Pekerjaan Remote)</option>
                        <option value="arbeitnow">🔵 Arbeitnow (Pekerjaan EU)</option>
                    </select>
                </div>

                {{-- Limit --}}
                <div>
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2 block">Maks Lowongan</label>
                    <select name="limit" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-white text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all">
                        <option value="10">10 lowongan</option>
                        <option value="20">20 lowongan</option>
                        <option value="30" selected>30 lowongan</option>
                        <option value="50">50 lowongan</option>
                        <option value="100">100 lowongan</option>
                    </select>
                </div>
            </div>

            {{-- API Info --}}
            <div class="bg-slate-900/50 p-5 rounded-xl border border-slate-800 mb-8">
                <h4 class="text-xs font-bold text-white uppercase tracking-widest mb-3">API Tersedia</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="flex items-start gap-3">
                        <span class="w-2 h-2 bg-red-500 rounded-full mt-1.5 shrink-0"></span>
                        <div>
                            <p class="text-sm font-bold text-white">JSearch (New York)</p>
                            <p class="text-[11px] text-slate-500">Loker riil teraktual dari Google Jobs New York (Butuh RAPIDAPI_KEY di .env)</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="w-2 h-2 bg-emerald-400 rounded-full mt-1.5 shrink-0"></span>
                        <div>
                            <p class="text-sm font-bold text-white">Remotive</p>
                            <p class="text-[11px] text-slate-500">Pekerjaan remote teknologi seluruh dunia, termasuk data gaji</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="w-2 h-2 bg-blue-400 rounded-full mt-1.5 shrink-0"></span>
                        <div>
                            <p class="text-sm font-bold text-white">Arbeitnow</p>
                            <p class="text-[11px] text-slate-500">Pekerjaan berfokus EU dengan tag untuk ekstraksi keahlian</p>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full btn-premium py-4 flex items-center justify-center gap-2" :disabled="loading">
                <template x-if="!loading">
                    <span><i class="fas fa-download mr-2"></i> Ambil Lowongan dari API</span>
                </template>
                <template x-if="loading">
                    <span><i class="fas fa-spinner fa-spin mr-2"></i> Mengambil data... mohon tunggu</span>
                </template>
            </button>
        </form>
    </div>
</div>
@endsection
