@extends('layouts.app')

@section('title', 'Kelola User')

@section('content')
<div class="max-w-6xl mx-auto space-y-8 animate-fade-in">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-500/10 text-purple-400 text-xs font-bold tracking-widest uppercase mb-3 border border-purple-500/20">
                <i class="fas fa-users-cog"></i>
                Panel Admin
            </div>
            <h1 class="text-3xl font-extrabold text-white mb-1">Kelola <span class="text-gradient">User</span></h1>
            <p class="text-slate-400 text-sm">Tambah, edit, atau hapus pengguna platform.</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="px-5 py-3 rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700 transition-all flex items-center gap-2 shadow-lg shadow-blue-600/20">
            <i class="fas fa-plus"></i> Tambah User Baru
        </a>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
    <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 p-4 rounded-xl flex items-center gap-3 animate-fade-in">
        <i class="fas fa-check-circle text-lg"></i>
        <p class="font-medium text-sm">{{ session('success') }}</p>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-500/10 border border-red-500/30 text-red-400 p-4 rounded-xl flex items-center gap-3 animate-fade-in">
        <i class="fas fa-exclamation-circle text-lg"></i>
        <p class="font-medium text-sm">{{ session('error') }}</p>
    </div>
    @endif

    {{-- Main Table Container --}}
    <div class="glass p-6 sm:p-8">
        
        {{-- Search and filter --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <form action="{{ route('admin.users.index') }}" method="GET" class="w-full md:w-96 flex gap-2">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, email, atau role..." 
                       class="w-full bg-slate-900/60 border border-slate-800 rounded-xl px-4 py-2.5 text-white text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all">
                <button type="submit" class="px-4 py-2.5 rounded-xl bg-slate-800 text-white hover:bg-slate-700 transition-all text-sm font-bold flex items-center gap-2 border border-slate-700">
                    <i class="fas fa-search"></i> Cari
                </button>
                @if($search)
                    <a href="{{ route('admin.users.index') }}" class="px-3 py-2.5 rounded-xl bg-red-500/10 text-red-500 hover:bg-red-500 hover:text-white transition-all text-sm font-bold flex items-center justify-center border border-red-500/20" title="Clear search">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </form>
            <div class="text-xs text-slate-500">
                Menampilkan {{ $users->firstItem() ?? 0 }}-{{ $users->lastItem() ?? 0 }} dari {{ $users->total() }} user
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto rounded-xl border border-slate-800/80 bg-slate-900/25">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-800/80 bg-slate-950/20 text-slate-400 text-xs font-bold uppercase tracking-wider">
                        <th class="py-4 px-6 w-16">No</th>
                        <th class="py-4 px-6">Nama</th>
                        <th class="py-4 px-6">Email</th>
                        <th class="py-4 px-6 w-32">Role</th>
                        <th class="py-4 px-6 w-40">Tanggal Terdaftar</th>
                        <th class="py-4 px-6 w-44 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 text-slate-300 text-sm">
                    @forelse($users as $idx => $user)
                    <tr class="hover:bg-slate-800/20 transition-all">
                        <td class="py-4 px-6 font-medium text-slate-500">{{ $users->firstItem() + $idx }}</td>
                        <td class="py-4 px-6 font-bold text-white">{{ $user->name }}</td>
                        <td class="py-4 px-6 text-slate-400">{{ $user->email }}</td>
                        <td class="py-4 px-6">
                            @if($user->isAdmin())
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-purple-500/10 text-purple-400 border border-purple-500/20">
                                <i class="fas fa-shield-halved text-[9px]"></i> Admin
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-blue-500/10 text-blue-400 border border-blue-500/20">
                                <i class="fas fa-user text-[9px]"></i> User
                            </span>
                            @endif
                        </td>
                        <td class="py-4 px-6 text-slate-500">{{ $user->created_at->format('d M Y') }}</td>
                        <td class="py-4 px-6 text-right">
                            <div class="inline-flex gap-2">
                                <a href="{{ route('admin.users.edit', $user) }}" class="p-2 rounded-lg bg-amber-500/10 text-amber-500 border border-amber-500/20 hover:bg-amber-500 hover:text-white transition-all" title="Edit User">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if(auth()->id() !== $user->id)
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 rounded-lg bg-red-500/10 text-red-500 border border-red-500/20 hover:bg-red-500 hover:text-white transition-all" title="Hapus User">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @else
                                <span class="p-2 rounded-lg bg-slate-800/50 text-slate-600 border border-slate-700/30 cursor-not-allowed" title="Anda tidak dapat menghapus diri sendiri">
                                    <i class="fas fa-trash"></i>
                                </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-8 text-slate-500 italic">
                            Tidak ada user ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
