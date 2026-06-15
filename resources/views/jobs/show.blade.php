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
                {{-- Skill Compatibility Widget --}}
                <div class="glass-dark p-6 rounded-2xl border border-slate-700/60 shadow-xl" data-aos="fade-up">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                        <div>
                            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                                <i class="fas fa-brain text-purple-400"></i> Kecocokan Skill Anda (AI Match)
                            </h3>
                            <p class="text-xs text-slate-500 mt-1">Berdasarkan data keahlian dari CV/Asesmen Anda</p>
                        </div>
                        
                        @if($hasCvOrAssessment && count($requiredSkills) > 0)
                            <div class="flex items-center gap-3">
                                <div class="text-right">
                                    <p class="text-xs text-slate-400 font-medium">Skor Kecocokan</p>
                                    <p class="text-xl font-black text-blue-400">{{ $matchPercent }}%</p>
                                </div>
                                <div class="relative w-12 h-12 flex items-center justify-center">
                                    <!-- Progress Circle SVG -->
                                    <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                                        <path class="text-slate-800" stroke-width="3.5" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                        <path class="text-blue-500 transition-all duration-1000" stroke-dasharray="{{ $matchPercent }}, 100" stroke-width="3.5" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                    </svg>
                                    <span class="absolute text-[10px] font-bold text-white">{{ $matchPercent }}%</span>
                                </div>
                            </div>
                        @endif
                    </div>

                    @if(!$hasCvOrAssessment)
                        {{-- No Skill Data State --}}
                        <div class="p-5 rounded-xl bg-slate-800/40 border border-slate-700/50 flex flex-col sm:flex-row items-center gap-4 text-center sm:text-left">
                            <div class="w-12 h-12 rounded-xl bg-amber-500/10 flex items-center justify-center text-amber-400 shrink-0 text-xl">
                                <i class="fas fa-file-invoice"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-white">Skill Anda belum terdeteksi</p>
                                <p class="text-xs text-slate-400 leading-relaxed mt-0.5">Unggah CV/Resume Anda atau ikuti Tes DNA Karir untuk menganalisis kecocokan skill dengan lowongan ini secara instan.</p>
                            </div>
                            <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto shrink-0">
                                <a href="{{ route('cv.index') }}" class="px-4 py-2 text-xs font-bold text-white bg-blue-600 hover:bg-blue-500 rounded-lg transition-all text-center">
                                    <i class="fas fa-file-pdf mr-1"></i> Unggah CV
                                </a>
                                <a href="{{ route('assessment.index') }}" class="px-4 py-2 text-xs font-bold text-slate-300 bg-slate-800 hover:bg-slate-700 rounded-lg transition-all text-center">
                                    <i class="fas fa-wand-magic-sparkles mr-1"></i> Tes DNA
                                </a>
                            </div>
                        </div>
                    @elseif(count($requiredSkills) === 0)
                        {{-- No Required Skills Found --}}
                        <div class="p-4 rounded-xl bg-slate-800/40 border border-slate-700/50 flex items-center gap-3">
                            <i class="fas fa-info-circle text-blue-400 text-lg"></i>
                            <p class="text-xs text-slate-400 leading-relaxed">
                                Lowongan ini tidak mencantumkan spesifikasi skill teknis khusus di deskripsinya. Silakan baca kualifikasi dan persyaratan di bawah untuk detail selengkapnya.
                            </p>
                        </div>
                    @else
                        {{-- Has Skill Data --}}
                        <div class="space-y-4">
                            <!-- Match Alert Message -->
                            @if($matchPercent === 100)
                                <div class="px-4 py-2.5 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-medium flex items-center gap-2">
                                    <i class="fas fa-circle-check"></i> Luar biasa! Seluruh skill Anda memenuhi kualifikasi lowongan ini.
                                </div>
                            @elseif($matchPercent >= 70)
                                <div class="px-4 py-2.5 rounded-lg bg-blue-500/10 border border-blue-500/20 text-blue-400 text-xs font-medium flex items-center gap-2">
                                    <i class="fas fa-thumbs-up"></i> Kesiapan Anda tinggi! Anda memenuhi sebagian besar kualifikasi yang dicari.
                                </div>
                            @else
                                <div class="px-4 py-2.5 rounded-lg bg-amber-500/10 border border-amber-500/20 text-amber-400 text-xs font-medium flex items-center gap-2">
                                    <i class="fas fa-triangle-exclamation"></i> Ada beberapa skill yang belum terdeteksi. Pelajari di Peta Belajar untuk meningkatkan peluang Anda!
                                </div>
                            @endif

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                {{-- Matched Skills --}}
                                <div class="p-4 rounded-xl bg-slate-800/20 border border-slate-800/60">
                                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Skill yang Kamu Miliki ({{ count($matchedSkills) }})
                                    </h4>
                                    @if(count($matchedSkills) > 0)
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($matchedSkills as $mSkill)
                                                <span class="px-3 py-1.5 rounded-lg bg-emerald-500/10 text-emerald-400 text-xs font-semibold border border-emerald-500/20 flex items-center gap-1.5 shadow-sm">
                                                    <i class="fas fa-check text-[10px]"></i> {{ $mSkill }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-xs text-slate-600 italic">Tidak ada kecocokan skill.</p>
                                    @endif
                                </div>

                                {{-- Missing Skills --}}
                                <div class="p-4 rounded-xl bg-slate-800/20 border border-slate-800/60">
                                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        Skill yang Masih Kurang ({{ count($missingSkills) }})
                                    </h4>
                                    @if(count($missingSkills) > 0)
                                        <div class="flex flex-wrap gap-2 mb-3">
                                            @foreach($missingSkills as $missSkill)
                                                <span class="px-3 py-1.5 rounded-lg bg-amber-500/10 text-amber-400 text-xs font-semibold border border-amber-500/20 flex items-center gap-1.5 shadow-sm">
                                                    <i class="fas fa-xmark text-[10px] text-amber-500/70"></i> {{ $missSkill }}
                                                </span>
                                            @endforeach
                                        </div>
                                        <div class="text-right">
                                            <a href="{{ route('roadmap.index') }}" class="text-[10px] font-bold text-blue-400 hover:text-blue-300 hover:underline transition-all flex items-center justify-end gap-1">
                                                <i class="fas fa-map-signs"></i> Buka Peta Belajar untuk menguasai skill ini <i class="fas fa-chevron-right text-[8px]"></i>
                                            </a>
                                        </div>
                                    @else
                                        <p class="text-xs text-slate-600 italic">Tidak ada kekurangan skill! Kualifikasi Anda lengkap.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

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
