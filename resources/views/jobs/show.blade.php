@extends('layouts.app')

@section('title', 'Detail Lowongan')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2 space-y-8">
        <!-- Main Job Info -->
        <div class="glass p-8" data-aos="fade-up">
            <div class="flex flex-col md:flex-row items-start justify-between gap-6 mb-8 pb-8 border-b border-slate-800">
                <div class="flex items-center gap-6">
                    <div class="w-20 h-20 bg-slate-800 rounded-3xl flex items-center justify-center">
                        <i class="fas fa-building text-slate-500 text-3xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-white mb-2">{{ $job->title }}</h1>
                        <div class="flex flex-wrap items-center gap-4 text-sm text-slate-400">
                            <span class="text-blue-500 font-bold"><i class="fas fa-building mr-1"></i> {{ $job->company_name }}</span>
                            <span><i class="fas fa-map-marker-alt mr-1"></i> {{ $job->location }}</span>
                            <span class="px-2 py-0.5 rounded-lg bg-slate-800 text-xs font-bold uppercase">{{ $job->type }}</span>
                        </div>
                    </div>
                </div>
                <div class="flex flex-wrap gap-3 w-full md:w-auto">
                    @php $isTracked = \App\Models\Application::where('user_id', Auth::id())->where('job_id', $job->id)->exists(); @endphp
                    @if($isTracked)
                        <span class="px-5 py-2.5 rounded-xl bg-emerald-500/10 text-emerald-400 text-sm font-bold">
                            <i class="fas fa-check-circle mr-1"></i> Sudah Dilacak
                        </span>
                    @else
                        <form action="{{ route('tracker.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="job_id" value="{{ $job->id }}">
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-sm transition-all">
                                <i class="fas fa-bookmark mr-1"></i> Simpan ke Pelacak
                            </button>
                        </form>
                    @endif
                    @if($job->url)
                    <a href="{{ $job->url }}" target="_blank" class="btn-premium px-10 flex items-center justify-center gap-2">Lamar Sekarang <i class="fas fa-external-link-alt text-xs"></i></a>
                    @else
                    <a href="https://www.google.com/search?q={{ urlencode('Lowongan Kerja ' . $job->title . ' ' . $job->company_name) }}" target="_blank" class="btn-premium px-10 flex items-center justify-center gap-2">Cari Lowongan Asli <i class="fas fa-search text-xs"></i></a>
                    @endif
                </div>
            </div>

            <div class="space-y-8">
                <div>
                    <h3 class="text-xl font-bold text-white mb-4">Deskripsi</h3>
                    <div class="text-slate-400 leading-relaxed space-y-4">
                        {!! nl2br(e($job->description)) !!}
                    </div>
                </div>

                <div>
                    <h3 class="text-xl font-bold text-white mb-4">Persyaratan</h3>
                    <div class="text-slate-400 leading-relaxed space-y-4">
                        {!! nl2br(e($job->requirements)) !!}
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Jobs -->
        <div data-aos="fade-up">
            <h3 class="text-xl font-bold text-white mb-6">Peluang Serupa</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($relatedJobs as $rJob)
                <a href="{{ route('jobs.show', $rJob->slug) }}" class="glass-dark p-6 card-hover flex items-center justify-between">
                    <div>
                        <h4 class="font-bold text-white">{{ $rJob->title }}</h4>
                        <p class="text-xs text-slate-500">{{ $rJob->company_name }}</p>
                    </div>
                    <i class="fas fa-arrow-right text-blue-500"></i>
                </a>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Sidebar Info -->
    <div class="space-y-6">
        <div class="glass-dark p-6" data-aos="fade-left">
            <h3 class="text-lg font-bold text-white mb-6">Ringkasan Lowongan</h3>
            <div class="space-y-6">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-blue-500/10 rounded-xl flex items-center justify-center text-blue-500">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-500 uppercase font-bold tracking-widest">Tanggal Posting</p>
                        <p class="text-sm font-bold text-white">{{ $job->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-emerald-500/10 rounded-xl flex items-center justify-center text-emerald-500">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-500 uppercase font-bold tracking-widest">Kisaran Gaji</p>
                        <p class="text-sm font-bold text-white">{{ $job->salary_range }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-purple-500/10 rounded-xl flex items-center justify-center text-purple-500">
                        <i class="fas fa-tags"></i>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-500 uppercase font-bold tracking-widest">Kategori</p>
                        <p class="text-sm font-bold text-white">{{ $job->category->name }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="glass-dark p-6" data-aos="fade-left" data-aos-delay="100">
            <h3 class="text-lg font-bold text-white mb-4">Tentang Perusahaan</h3>
            <p class="text-sm text-slate-400 mb-6 leading-relaxed">Perusahaan ini adalah pemain terkemuka di sektor {{ $job->category->name }}, dikenal dengan inovasi dan komitmennya terhadap pengembangan karyawan.</p>
            <a href="https://www.google.com/search?q={{ urlencode($job->company_name . ' official website') }}" target="_blank" class="text-blue-500 text-sm font-bold hover:underline">Kunjungi Website <i class="fas fa-external-link-alt ml-1 text-[10px]"></i></a>
        </div>
    </div>
</div>
@endsection
