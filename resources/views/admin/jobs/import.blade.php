@extends('layouts.app')

@section('title', 'Impor Data Lowongan')

@section('content')
<div class="max-w-5xl mx-auto space-y-8" x-data="{ activeTab: 'api' }">

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

    {{-- Tab Switcher --}}
    <div class="flex gap-2 bg-slate-900 p-1.5 rounded-2xl animate-fade-in" style="animation-delay: 200ms">
        <button @click="activeTab = 'api'" class="flex-1 py-3 rounded-xl text-sm font-bold transition-all flex items-center justify-center gap-2"
                :class="activeTab === 'api' ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' : 'text-slate-400 hover:text-white'">
            <i class="fas fa-globe"></i> Ambil dari API
        </button>
        <button @click="activeTab = 'csv'" class="flex-1 py-3 rounded-xl text-sm font-bold transition-all flex items-center justify-center gap-2"
                :class="activeTab === 'csv' ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/20' : 'text-slate-400 hover:text-white'">
            <i class="fas fa-file-csv"></i> Unggah Dataset CSV
        </button>
    </div>

    {{-- API Fetch Tab --}}
    <div x-show="activeTab === 'api'" x-transition class="glass p-8 animate-fade-in">
        <div class="flex items-center gap-4 mb-8 pb-6 border-b border-slate-800">
            <div class="w-14 h-14 bg-blue-600/10 rounded-2xl flex items-center justify-center">
                <i class="fas fa-cloud-arrow-down text-blue-400 text-2xl"></i>
            </div>
            <div>
                <h2 class="text-xl font-bold text-white">Ambil dari API Publik</h2>
                <p class="text-slate-400 text-xs">Ambil lowongan kerja nyata dari Remotive & Arbeitnow (tanpa API key)</p>
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
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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

    {{-- CSV Upload Tab --}}
    <div x-show="activeTab === 'csv'" x-transition class="glass p-8 animate-fade-in">
        <div class="flex items-center gap-4 mb-8 pb-6 border-b border-slate-800">
            <div class="w-14 h-14 bg-emerald-600/10 rounded-2xl flex items-center justify-center">
                <i class="fas fa-file-csv text-emerald-400 text-2xl"></i>
            </div>
            <div>
                <h2 class="text-xl font-bold text-white">Unggah Dataset CSV</h2>
                <p class="text-slate-400 text-xs">Impor lowongan dari dataset Kaggle atau file CSV kustom</p>
            </div>
        </div>

        <form action="{{ route('admin.jobs.import.csv') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="space-y-8">
                <div class="border-2 border-dashed border-slate-700 rounded-3xl p-12 text-center hover:border-emerald-500 transition-all group relative" x-data="{ fileName: '' }">
                    <input type="file" name="csv_file" accept=".csv,.txt"
                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                           @change="fileName = $event.target.files[0]?.name || ''" required>
                    <div class="space-y-4">
                        <i class="fas fa-cloud-upload-alt text-4xl text-slate-600 group-hover:text-emerald-400 transition-all"></i>
                        <div class="text-white font-medium" x-text="fileName || 'Klik untuk unggah atau seret dan lepas'"></div>
                        <p class="text-xs text-slate-500">Hanya file CSV (Maks 10MB)</p>
                    </div>
                </div>

                {{-- Column Mapping Guide --}}
                <div class="bg-slate-900/50 p-6 rounded-2xl border border-slate-800">
                    <h4 class="text-sm font-bold text-white mb-4 uppercase tracking-widest">Pemetaan Kolom Fleksibel</h4>
                    <p class="text-xs text-slate-500 mb-4">Importer otomatis mendeteksi nama kolom berikut (tidak peka huruf besar/kecil):</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div class="flex items-center gap-2 text-xs">
                            <span class="w-2 h-2 bg-emerald-400 rounded-full shrink-0"></span>
                            <span class="text-white font-bold">Judul:</span>
                            <span class="text-slate-500">job_title, title, position, role</span>
                        </div>
                        <div class="flex items-center gap-2 text-xs">
                            <span class="w-2 h-2 bg-emerald-400 rounded-full shrink-0"></span>
                            <span class="text-white font-bold">Perusahaan:</span>
                            <span class="text-slate-500">company_name, company, employer</span>
                        </div>
                        <div class="flex items-center gap-2 text-xs">
                            <span class="w-2 h-2 bg-blue-400 rounded-full shrink-0"></span>
                            <span class="text-white font-bold">Lokasi:</span>
                            <span class="text-slate-500">location, city, job_location</span>
                        </div>
                        <div class="flex items-center gap-2 text-xs">
                            <span class="w-2 h-2 bg-blue-400 rounded-full shrink-0"></span>
                            <span class="text-white font-bold">Gaji:</span>
                            <span class="text-slate-500">salary, salary_range, compensation</span>
                        </div>
                        <div class="flex items-center gap-2 text-xs">
                            <span class="w-2 h-2 bg-amber-400 rounded-full shrink-0"></span>
                            <span class="text-white font-bold">Deskripsi:</span>
                            <span class="text-slate-500">description, job_description, details</span>
                        </div>
                        <div class="flex items-center gap-2 text-xs">
                            <span class="w-2 h-2 bg-amber-400 rounded-full shrink-0"></span>
                            <span class="text-white font-bold">Keahlian:</span>
                            <span class="text-slate-500">skills, requirements, qualifications</span>
                        </div>
                        <div class="flex items-center gap-2 text-xs">
                            <span class="w-2 h-2 bg-purple-400 rounded-full shrink-0"></span>
                            <span class="text-white font-bold">Kategori:</span>
                            <span class="text-slate-500">category, industry, department</span>
                        </div>
                        <div class="flex items-center gap-2 text-xs">
                            <span class="w-2 h-2 bg-purple-400 rounded-full shrink-0"></span>
                            <span class="text-white font-bold">Tipe:</span>
                            <span class="text-slate-500">type, job_type, employment_type</span>
                        </div>
                    </div>
                    <div class="mt-5 pt-4 border-t border-slate-800">
                        <p class="text-[10px] text-slate-600 mb-1">Contoh header CSV:</p>
                        <code class="text-[11px] text-emerald-400 bg-slate-900 px-3 py-1.5 rounded-lg inline-block">title,company_name,location,salary,category,description,skills</code>
                    </div>
                </div>

                <button type="submit" class="w-full py-4 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-bold shadow-lg shadow-emerald-500/20 hover:shadow-emerald-500/40 transition-all">
                    <i class="fas fa-file-import mr-2"></i> Impor dari CSV
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
