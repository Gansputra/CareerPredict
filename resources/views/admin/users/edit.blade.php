@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="max-w-3xl mx-auto space-y-8 animate-fade-in">

    {{-- Header --}}
    <div>
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 text-slate-400 hover:text-white transition-colors text-sm font-semibold mb-4">
            <i class="fas fa-arrow-left"></i> Kembali ke Kelola User
        </a>
        <h1 class="text-3xl font-extrabold text-white mb-1">Edit <span class="text-gradient">User</span></h1>
        <p class="text-slate-400 text-sm">Perbarui informasi profil atau perubah role pengguna.</p>
    </div>

    {{-- Form Container --}}
    <div class="glass p-6 sm:p-8">
        
        <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-6">
            @csrf
            @method('PATCH')
            
            {{-- Name --}}
            <div>
                <label for="name" class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2 block">Nama Lengkap</label>
                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" placeholder="Masukkan nama lengkap..." required
                       class="w-full bg-slate-900/60 border @error('name') border-red-500 @else border-slate-800 @enderror rounded-xl px-4 py-3 text-white text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all">
                @error('name')
                    <p class="text-red-400 text-xs mt-1.5"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div>
                <label for="email" class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2 block">Alamat Email</label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" placeholder="Masukkan alamat email..." required
                       class="w-full bg-slate-900/60 border @error('email') border-red-500 @else border-slate-800 @enderror rounded-xl px-4 py-3 text-white text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all">
                @error('email')
                    <p class="text-red-400 text-xs mt-1.5"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                @enderror
            </div>

            {{-- Role --}}
            <div>
                <label for="role" class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2 block">Role Akses</label>
                <select name="role" id="role" required
                        class="w-full bg-slate-900/60 border @error('role') border-red-500 @else border-slate-800 @enderror rounded-xl px-4 py-3 text-white text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all">
                    <option value="user" {{ old('role', $user->role) === 'user' ? 'selected' : '' }}>User (Pengguna Umum)</option>
                    <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin (Administrator Sistem)</option>
                </select>
                @error('role')
                    <p class="text-red-400 text-xs mt-1.5"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                @enderror
            </div>

            {{-- Password Info Alert --}}
            <div class="p-4 rounded-xl bg-slate-800/40 border border-slate-700/50 flex gap-3 text-slate-400 text-xs">
                <i class="fas fa-info-circle text-blue-400 text-sm mt-0.5 shrink-0"></i>
                <p>Biarkan kolom kata sandi kosong jika Anda tidak ingin mengubah kata sandi pengguna saat ini.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Password --}}
                <div>
                    <label for="password" class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2 block">Kata Sandi Baru</label>
                    <input type="password" name="password" id="password" placeholder="Masukkan kata sandi baru..."
                           class="w-full bg-slate-900/60 border @error('password') border-red-500 @else border-slate-800 @enderror rounded-xl px-4 py-3 text-white text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all">
                    @error('password')
                        <p class="text-red-400 text-xs mt-1.5"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                    @enderror
                </div>

                {{-- Confirm Password --}}
                <div>
                    <label for="password_confirmation" class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2 block">Konfirmasi Kata Sandi Baru</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Ulangi kata sandi baru..."
                           class="w-full bg-slate-900/60 border border-slate-800 rounded-xl px-4 py-3 text-white text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all">
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-4 pt-4 border-t border-slate-800/80">
                <a href="{{ route('admin.users.index') }}" class="px-5 py-3 rounded-xl bg-slate-800 text-white font-bold hover:bg-slate-700 transition-all text-sm border border-slate-700">
                    Batal
                </a>
                <button type="submit" class="px-5 py-3 rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700 transition-all text-sm shadow-lg shadow-blue-600/20">
                    Perbarui User
                </button>
            </div>

        </form>

    </div>
</div>
@endsection
