@extends('layouts.app')

@section('title', 'Edit Sponsor')

@section('content')
<div class="max-w-4xl mx-auto space-y-8 animate-fade-in">

    {{-- Header --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.sponsors.index') }}" class="p-2.5 rounded-xl bg-slate-800/50 hover:bg-slate-700 text-slate-400 hover:text-white transition-all border border-slate-700/50">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-500/10 text-purple-400 text-xs font-bold tracking-widest uppercase mb-2 border border-purple-500/20">
                <i class="fas fa-edit"></i>
                Edit Sponsor
            </div>
            <h1 class="text-3xl font-extrabold text-white mb-1">Edit <span class="text-gradient">Sponsor</span></h1>
            <p class="text-slate-400 text-sm">Perbarui detail banner sponsor dan simpan perubahan Anda.</p>
        </div>
    </div>

    {{-- Form Container --}}
    <div class="glass p-8 relative overflow-hidden">
        <form action="{{ route('admin.sponsors.update', $sponsor) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Title --}}
                <div class="space-y-2">
                    <label for="title" class="block text-xs font-bold text-slate-400 uppercase tracking-widest">Nama Sponsor</label>
                    <input type="text" name="title" id="title" value="{{ old('title', $sponsor->title) }}" placeholder="Contoh: Ganesha Operation" required
                           class="w-full bg-slate-900/60 border border-slate-800/80 rounded-xl px-4 py-3 text-white text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all @error('title') border-red-500 @enderror">
                    @error('title')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- CTA Text --}}
                <div class="space-y-2">
                    <label for="cta_text" class="block text-xs font-bold text-slate-400 uppercase tracking-widest">Teks Tombol CTA</label>
                    <input type="text" name="cta_text" id="cta_text" value="{{ old('cta_text', $sponsor->cta_text) }}" placeholder="Contoh: Daftar Sekarang, Kunjungi" required
                           class="w-full bg-slate-900/60 border border-slate-800/80 rounded-xl px-4 py-3 text-white text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all @error('cta_text') border-red-500 @enderror">
                    @error('cta_text')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Link URL --}}
            <div class="space-y-2">
                <label for="link_url" class="block text-xs font-bold text-slate-400 uppercase tracking-widest">Tautan URL Sponsor (Redirect Link)</label>
                <input type="url" name="link_url" id="link_url" value="{{ old('link_url', $sponsor->link_url) }}" placeholder="Contoh: https://ganeshaoperation.com/promo" required
                       class="w-full bg-slate-900/60 border border-slate-800/80 rounded-xl px-4 py-3 text-white text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all @error('link_url') border-red-500 @enderror">
                @error('link_url')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Sort Order --}}
                <div class="space-y-2">
                    <label for="order" class="block text-xs font-bold text-slate-400 uppercase tracking-widest">Urutan Tampilan</label>
                    <input type="number" name="order" id="order" value="{{ old('order', $sponsor->order) }}" min="0" required
                           class="w-full bg-slate-900/60 border border-slate-800/80 rounded-xl px-4 py-3 text-white text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all @error('order') border-red-500 @enderror">
                    @error('order')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Status --}}
                <div class="flex items-center h-full pt-8">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', $sponsor->is_active) ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-slate-800 rounded-full peer peer-focus:ring-2 peer-focus:ring-blue-600/50 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-slate-400 after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600 peer-checked:after:bg-white"></div>
                        <span class="ml-3 text-sm font-medium text-slate-400">Aktifkan Banner</span>
                    </label>
                </div>
            </div>

            {{-- Image File with Preview --}}
            <div class="space-y-3">
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest">File Gambar Banner (Biarkan kosong jika tidak ingin mengubah)</label>
                
                {{-- Drag & Drop styled area --}}
                <div class="relative border-2 border-dashed border-slate-800 hover:border-blue-500/50 bg-slate-950/20 rounded-2xl p-6 transition-all flex flex-col items-center justify-center text-center cursor-pointer group">
                    <input type="file" name="image" id="image" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer z-10" onchange="previewImage(event)">
                    
                    {{-- Default Image Preview --}}
                    <div id="image-preview-container" class="w-full max-w-xl space-y-4">
                        <div class="rounded-xl overflow-hidden border border-slate-800 aspect-[4/1] w-full bg-slate-900">
                            <img id="image-preview" src="{{ asset('storage/' . $sponsor->image_path) }}" alt="Preview Banner" class="w-full h-full object-cover">
                        </div>
                        <p class="text-xs text-blue-400 font-medium"><i class="fas fa-check-circle mr-1"></i> Klik atau seret berkas baru untuk mengganti gambar banner ini.</p>
                    </div>
                </div>
                @error('image')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Action Buttons --}}
            <div class="flex justify-end gap-3 pt-6 border-t border-slate-800/80">
                <a href="{{ route('admin.sponsors.index') }}" class="px-5 py-3 rounded-xl bg-slate-800 hover:bg-slate-750 text-slate-300 font-bold transition-all text-sm">Batal</a>
                <button type="submit" class="px-8 py-3 rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700 transition-all text-sm shadow-lg shadow-blue-600/20">
                    <i class="fas fa-save mr-2"></i> Perbarui Banner
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function previewImage(event) {
        const input = event.target;
        const reader = new FileReader();
        
        reader.onload = function() {
            const preview = document.getElementById('image-preview');
            preview.src = reader.result;
        };
        
        if (input.files && input.files[0]) {
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
