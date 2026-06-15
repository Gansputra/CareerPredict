@extends('layouts.app')

@section('title', 'Kelola Sponsor')

@section('content')
<div class="max-w-6xl mx-auto space-y-8 animate-fade-in">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-500/10 text-purple-400 text-xs font-bold tracking-widest uppercase mb-3 border border-purple-500/20">
                <i class="fas fa-ad"></i>
                Panel Admin
            </div>
            <h1 class="text-3xl font-extrabold text-white mb-1">Kelola <span class="text-gradient">Sponsor</span></h1>
            <p class="text-slate-400 text-sm">Unggah banner dan tautan sponsor untuk ditampilkan di dashboard siswa.</p>
        </div>
        <a href="{{ route('admin.sponsors.create') }}" class="px-5 py-3 rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700 transition-all flex items-center gap-2 shadow-lg shadow-blue-600/20">
            <i class="fas fa-plus"></i> Tambah Banner Baru
        </a>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
    <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 p-4 rounded-xl flex items-center gap-3 animate-fade-in">
        <i class="fas fa-check-circle text-lg"></i>
        <p class="font-medium text-sm">{{ session('success') }}</p>
    </div>
    @endif

    {{-- Layout: Table & Guidelines --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- Guidelines Card --}}
        <div class="glass-dark p-6 space-y-6 h-fit border border-slate-800/80">
            <h3 class="text-lg font-bold text-white"><i class="fas fa-info-circle text-blue-400 mr-2"></i> Panduan Desain Banner</h3>
            <div class="space-y-4 text-slate-300 text-sm leading-relaxed">
                <div>
                    <h4 class="font-bold text-white text-xs uppercase tracking-wider text-slate-400 mb-1">Rasio Layar (Aspect Ratio)</h4>
                    <p>Rekomendasi rasio adalah <strong>4:1 hingga 8:1</strong>. Format lanskap lebar sangat optimal untuk tampilan dashboard.</p>
                </div>
                <div>
                    <h4 class="font-bold text-white text-xs uppercase tracking-wider text-slate-400 mb-1">Dimensi Piksel Ideal</h4>
                    <ul class="list-disc pl-4 space-y-1">
                        <li><strong>1200 x 300 px</strong> (Desktop)</li>
                        <li><strong>1200 x 240 px</strong> (Medium)</li>
                        <li><strong>1200 x 150 px</strong> (Compact)</li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-white text-xs uppercase tracking-wider text-slate-400 mb-1">Ketentuan Berkas</h4>
                    <ul class="list-disc pl-4 space-y-1">
                        <li>Format: <strong>WebP</strong> (Sangat Disarankan), PNG, JPG, GIF.</li>
                        <li>Ukuran Maksimal: <strong>2 MB</strong>.</li>
                    </ul>
                </div>
                <div class="p-3 bg-blue-500/5 border border-blue-500/10 rounded-xl text-xs text-blue-400">
                    <i class="fas fa-lightbulb mr-1"></i> <strong>Tips:</strong> Pastikan teks pada banner memiliki kontras yang cukup tinggi terhadap latar belakang gambar agar tetap terbaca dengan jelas pada berbagai ukuran layar.
                </div>
            </div>
        </div>

        {{-- Table Container --}}
        <div class="lg:col-span-2 glass p-6 sm:p-8">
            <h3 class="text-lg font-bold text-white mb-6"><i class="fas fa-list-ul text-purple-400 mr-2"></i> Daftar Banner Aktif</h3>

            <div class="overflow-x-auto rounded-xl border border-slate-800/80 bg-slate-900/25">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-800/80 bg-slate-950/20 text-slate-400 text-xs font-bold uppercase tracking-wider">
                            <th class="py-4 px-6 w-16">No</th>
                            <th class="py-4 px-6 w-40">Preview</th>
                            <th class="py-4 px-6">Sponsor</th>
                            <th class="py-4 px-6 w-24 text-center">Status</th>
                            <th class="py-4 px-6 w-20 text-center">Urutan</th>
                            <th class="py-4 px-6 w-32 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60 text-slate-300 text-sm">
                        @forelse($sponsors as $idx => $sponsor)
                        <tr class="hover:bg-slate-800/20 transition-all">
                            <td class="py-4 px-6 font-medium text-slate-500">{{ $idx + 1 }}</td>
                            <td class="py-4 px-6">
                                <div class="w-32 h-10 rounded-lg overflow-hidden border border-slate-700 bg-slate-800/50">
                                    <img src="{{ asset('storage/' . $sponsor->image_path) }}" alt="Preview" class="w-full h-full object-cover">
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <p class="font-bold text-white leading-tight mb-1">{{ $sponsor->title }}</p>
                                <a href="{{ $sponsor->link_url }}" target="_blank" class="text-xs text-blue-400 hover:underline flex items-center gap-1 max-w-[180px] truncate" title="{{ $sponsor->link_url }}">
                                    <i class="fas fa-external-link-alt text-[9px]"></i> {{ $sponsor->link_url }}
                                </a>
                            </td>
                            <td class="py-4 px-6 text-center">
                                @if($sponsor->is_active)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                    Aktif
                                </span>
                                @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-500/10 text-slate-400 border border-slate-700/30">
                                    Mati
                                </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 font-medium text-slate-400 text-center">{{ $sponsor->order }}</td>
                            <td class="py-4 px-6 text-right">
                                <div class="inline-flex gap-2">
                                    <a href="{{ route('admin.sponsors.edit', $sponsor) }}" class="p-2 rounded-lg bg-amber-500/10 text-amber-500 border border-amber-500/20 hover:bg-amber-500 hover:text-white transition-all" title="Edit Sponsor">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.sponsors.destroy', $sponsor) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus banner sponsor ini?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 rounded-lg bg-red-500/10 text-red-500 border border-red-500/20 hover:bg-red-500 hover:text-white transition-all" title="Hapus Sponsor">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-12 text-slate-500 italic">
                                Belum ada banner sponsor yang diunggah.<br>
                                <a href="{{ route('admin.sponsors.create') }}" class="text-blue-500 hover:underline text-xs mt-2 inline-block">Klik di sini untuk mengunggah banner pertama.</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>
@endsection
