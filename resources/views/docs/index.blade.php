@extends('layouts.app')

@section('title', 'Dokumentasi')

@section('content')
<div class="max-w-5xl mx-auto space-y-6 sm:space-y-8" x-data="{ activeSection: null }">

    {{-- ── Header ─────────────────────────────────────────────────────────────── --}}
    <div data-aos="fade-up">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/10 text-blue-400 text-[10px] sm:text-xs font-bold tracking-widest uppercase mb-3 border border-blue-500/20">
            <i class="fas fa-book"></i>
            Panduan Pengguna
        </div>
        <h1 class="text-2xl sm:text-3xl font-bold text-white mb-2">
            Dokumentasi <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-indigo-400">CareerPredict</span>
        </h1>
        <p class="text-slate-400 text-xs sm:text-sm max-w-2xl">
            Pelajari cara menggunakan setiap fitur utama CareerPredict untuk memaksimalkan perjalanan karir Anda. Klik pada setiap bagian untuk membaca panduan lengkapnya.
        </p>
    </div>

    {{-- ── Quick Nav ──────────────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 sm:gap-3" data-aos="fade-up" data-aos-delay="100">
        <button @click="activeSection = activeSection === 'dna' ? null : 'dna'; $nextTick(() => document.getElementById('section-dna')?.scrollIntoView({behavior:'smooth', block:'start'}))"
                class="glass-dark p-3 sm:p-4 rounded-2xl text-center hover:-translate-y-1 transition-all duration-300 group"
                :class="activeSection === 'dna' ? 'ring-2 ring-purple-500' : ''">
            <div class="w-10 h-10 sm:w-12 sm:h-12 mx-auto rounded-xl bg-purple-500/20 flex items-center justify-center text-purple-400 text-lg sm:text-xl mb-2 sm:mb-3 group-hover:scale-110 transition-transform">
                <i class="fas fa-wand-magic-sparkles"></i>
            </div>
            <p class="text-xs sm:text-sm font-bold text-white">Tes DNA Karir</p>
        </button>

        <button @click="activeSection = activeSection === 'cv' ? null : 'cv'; $nextTick(() => document.getElementById('section-cv')?.scrollIntoView({behavior:'smooth', block:'start'}))"
                class="glass-dark p-3 sm:p-4 rounded-2xl text-center hover:-translate-y-1 transition-all duration-300 group"
                :class="activeSection === 'cv' ? 'ring-2 ring-blue-500' : ''">
            <div class="w-10 h-10 sm:w-12 sm:h-12 mx-auto rounded-xl bg-blue-500/20 flex items-center justify-center text-blue-400 text-lg sm:text-xl mb-2 sm:mb-3 group-hover:scale-110 transition-transform">
                <i class="fas fa-file-pdf"></i>
            </div>
            <p class="text-xs sm:text-sm font-bold text-white">Analisis CV</p>
        </button>

        <button @click="activeSection = activeSection === 'jobs' ? null : 'jobs'; $nextTick(() => document.getElementById('section-jobs')?.scrollIntoView({behavior:'smooth', block:'start'}))"
                class="glass-dark p-3 sm:p-4 rounded-2xl text-center hover:-translate-y-1 transition-all duration-300 group"
                :class="activeSection === 'jobs' ? 'ring-2 ring-emerald-500' : ''">
            <div class="w-10 h-10 sm:w-12 sm:h-12 mx-auto rounded-xl bg-emerald-500/20 flex items-center justify-center text-emerald-400 text-lg sm:text-xl mb-2 sm:mb-3 group-hover:scale-110 transition-transform">
                <i class="fas fa-briefcase"></i>
            </div>
            <p class="text-xs sm:text-sm font-bold text-white">Jelajahi Lowongan</p>
        </button>

        <button @click="activeSection = activeSection === 'tracker' ? null : 'tracker'; $nextTick(() => document.getElementById('section-tracker')?.scrollIntoView({behavior:'smooth', block:'start'}))"
                class="glass-dark p-3 sm:p-4 rounded-2xl text-center hover:-translate-y-1 transition-all duration-300 group"
                :class="activeSection === 'tracker' ? 'ring-2 ring-amber-500' : ''">
            <div class="w-10 h-10 sm:w-12 sm:h-12 mx-auto rounded-xl bg-amber-500/20 flex items-center justify-center text-amber-400 text-lg sm:text-xl mb-2 sm:mb-3 group-hover:scale-110 transition-transform">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <p class="text-xs sm:text-sm font-bold text-white">Pelacak Lamaran</p>
        </button>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════════
         SECTION 1: TES DNA KARIR
    ══════════════════════════════════════════════════════════════════════════ --}}
    <div id="section-dna" class="scroll-mt-24" data-aos="fade-up" data-aos-delay="150">
        <div class="glass-dark rounded-2xl overflow-hidden border border-purple-500/20 hover:border-purple-500/40 transition-colors">
            <button @click="activeSection = activeSection === 'dna' ? null : 'dna'"
                    class="w-full flex items-center justify-between px-4 sm:px-8 py-4 sm:py-6 text-left group">
                <div class="flex items-center gap-3 sm:gap-4">
                    <div class="w-11 h-11 sm:w-14 sm:h-14 shrink-0 rounded-xl sm:rounded-2xl bg-purple-500/20 border border-purple-500/30 flex items-center justify-center text-purple-400 text-xl sm:text-2xl shadow-lg shadow-purple-500/10">
                        <i class="fas fa-wand-magic-sparkles"></i>
                    </div>
                    <div class="min-w-0">
                        <h2 class="text-base sm:text-xl font-bold text-white group-hover:text-purple-400 transition-colors">Tes DNA Karir</h2>
                        <p class="text-[10px] sm:text-xs text-slate-500 mt-0.5 truncate">Asesmen kepribadian berbasis Certainty Factor</p>
                    </div>
                </div>
                <i class="fas fa-chevron-down text-slate-500 transition-transform duration-300 ml-2 shrink-0"
                   :class="activeSection === 'dna' ? 'rotate-180 text-purple-400' : ''"></i>
            </button>

            <div x-show="activeSection === 'dna'"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 -translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-4"
                 style="display:none">
                <div class="px-4 sm:px-8 pb-6 sm:pb-8 space-y-4 sm:space-y-6">
                    <div class="h-px bg-gradient-to-r from-transparent via-purple-500/30 to-transparent"></div>

                    {{-- Apa Itu --}}
                    <div>
                        <h3 class="text-xs sm:text-sm font-bold text-purple-400 uppercase tracking-widest mb-2 sm:mb-3"><i class="fas fa-circle-info mr-2"></i>Apa Itu Tes DNA Karir?</h3>
                        <p class="text-slate-300 text-xs sm:text-sm leading-relaxed">
                            Tes DNA Karir adalah asesmen mendalam yang menganalisis <strong class="text-white">5 dimensi kepribadian</strong> Anda: Analitis, Kreatif, Teknis, Komunikasi, dan Kepemimpinan. Setiap dimensi memiliki 6 pertanyaan yang dirancang khusus untuk mengukur kecenderungan profesional Anda menggunakan metode <strong class="text-white">Certainty Factor (CF)</strong> — algoritma kecerdasan buatan yang juga dipakai dalam sistem pakar medis.
                        </p>
                    </div>

                    {{-- Cara Menggunakan --}}
                    <div>
                        <h3 class="text-xs sm:text-sm font-bold text-purple-400 uppercase tracking-widest mb-2 sm:mb-3"><i class="fas fa-list-ol mr-2"></i>Cara Menggunakan</h3>
                        <div class="space-y-2 sm:space-y-3">
                            @php
                                $dnaSteps = [
                                    ['icon' => 'fa-play-circle', 'title' => 'Buka Menu "Tes DNA Karir"', 'desc' => 'Klik menu <strong>Tes DNA Karir</strong> di sidebar kiri. Anda akan melihat halaman asesmen dengan 5 kategori kepribadian yang siap diisi.'],
                                    ['icon' => 'fa-sliders', 'title' => 'Jawab Setiap Pernyataan', 'desc' => 'Untuk setiap pernyataan, geser slider dari <strong>Sangat Tidak Setuju (1)</strong> hingga <strong>Sangat Setuju (5)</strong> sesuai dengan kepribadian Anda. Jawab sejujur mungkin untuk hasil yang akurat.'],
                                    ['icon' => 'fa-forward', 'title' => 'Navigasi Antar Kategori', 'desc' => 'Gunakan tombol <strong>Selanjutnya</strong> dan <strong>Sebelumnya</strong> untuk berpindah antar kategori. Progress Anda tersimpan otomatis selama sesi berlangsung.'],
                                    ['icon' => 'fa-paper-plane', 'title' => 'Kirim & Lihat Hasil', 'desc' => 'Setelah menyelesaikan semua 30 pertanyaan, klik <strong>Kirim Asesmen</strong>. Sistem akan menghitung skor CF Anda dan menampilkan daftar karir yang paling cocok beserta tingkat kepercayaan (confidence) masing-masing.'],
                                    ['icon' => 'fa-chart-radar', 'title' => 'Periksa Grafik Radar', 'desc' => 'Di halaman Dashboard, grafik radar <strong>Insight Kepribadian</strong> akan otomatis menampilkan profil 5 dimensi Anda secara visual.'],
                                ];
                            @endphp
                            @foreach($dnaSteps as $i => $step)
                            <div class="flex gap-3 sm:gap-4 p-3 sm:p-4 rounded-xl bg-slate-800/50 border border-slate-700/50">
                                <div class="w-7 h-7 sm:w-8 sm:h-8 shrink-0 rounded-lg bg-purple-500/20 flex items-center justify-center text-purple-400 text-xs sm:text-sm font-extrabold">
                                    {{ $i + 1 }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs sm:text-sm font-bold text-white mb-0.5 sm:mb-1"><i class="fas {{ $step['icon'] }} text-purple-400 mr-1.5 hidden sm:inline"></i>{{ $step['title'] }}</p>
                                    <p class="text-[11px] sm:text-xs text-slate-400 leading-relaxed">{!! $step['desc'] !!}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Tips --}}
                    <div class="flex gap-3 sm:gap-4 p-3 sm:p-4 rounded-xl bg-amber-500/5 border border-amber-500/20">
                        <div class="w-9 h-9 sm:w-10 sm:h-10 shrink-0 bg-amber-500/20 rounded-xl flex items-center justify-center text-amber-400">
                            <i class="fas fa-lightbulb"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="font-semibold text-white text-xs sm:text-sm mb-1">💡 Tips Pro</p>
                            <p class="text-[11px] sm:text-xs text-slate-400 leading-relaxed">
                                Anda bisa mengulang asesmen kapan saja dengan menekan tombol <strong class="text-slate-300">Reset</strong> di halaman asesmen. Jawaban baru akan menggantikan hasil sebelumnya dan memperbarui rekomendasi karir Anda secara otomatis.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════════
         SECTION 2: ANALISIS CV
    ══════════════════════════════════════════════════════════════════════════ --}}
    <div id="section-cv" class="scroll-mt-24" data-aos="fade-up" data-aos-delay="200">
        <div class="glass-dark rounded-2xl overflow-hidden border border-blue-500/20 hover:border-blue-500/40 transition-colors">
            <button @click="activeSection = activeSection === 'cv' ? null : 'cv'"
                    class="w-full flex items-center justify-between px-4 sm:px-8 py-4 sm:py-6 text-left group">
                <div class="flex items-center gap-3 sm:gap-4">
                    <div class="w-11 h-11 sm:w-14 sm:h-14 shrink-0 rounded-xl sm:rounded-2xl bg-blue-500/20 border border-blue-500/30 flex items-center justify-center text-blue-400 text-xl sm:text-2xl shadow-lg shadow-blue-500/10">
                        <i class="fas fa-file-pdf"></i>
                    </div>
                    <div class="min-w-0">
                        <h2 class="text-base sm:text-xl font-bold text-white group-hover:text-blue-400 transition-colors">Analisis CV</h2>
                        <p class="text-[10px] sm:text-xs text-slate-500 mt-0.5 truncate">Ekstraksi keahlian otomatis dari CV Anda</p>
                    </div>
                </div>
                <i class="fas fa-chevron-down text-slate-500 transition-transform duration-300 ml-2 shrink-0"
                   :class="activeSection === 'cv' ? 'rotate-180 text-blue-400' : ''"></i>
            </button>

            <div x-show="activeSection === 'cv'"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 -translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-4"
                 style="display:none">
                <div class="px-4 sm:px-8 pb-6 sm:pb-8 space-y-4 sm:space-y-6">
                    <div class="h-px bg-gradient-to-r from-transparent via-blue-500/30 to-transparent"></div>

                    <div>
                        <h3 class="text-xs sm:text-sm font-bold text-blue-400 uppercase tracking-widest mb-2 sm:mb-3"><i class="fas fa-circle-info mr-2"></i>Apa Itu Analisis CV?</h3>
                        <p class="text-slate-300 text-xs sm:text-sm leading-relaxed">
                            Fitur Analisis CV menggunakan teknologi <strong class="text-white">Natural Language Processing (NLP)</strong> untuk memindai dokumen CV Anda secara otomatis. Sistem akan mendeteksi keahlian teknis (seperti PHP, Python, React), soft skills (seperti Kepemimpinan, Komunikasi), serta minat profesional yang tertulis dalam CV. Hasil analisis ini akan digunakan untuk memperkaya profil Anda dan meningkatkan akurasi rekomendasi karir.
                        </p>
                    </div>

                    <div>
                        <h3 class="text-xs sm:text-sm font-bold text-blue-400 uppercase tracking-widest mb-2 sm:mb-3"><i class="fas fa-list-ol mr-2"></i>Cara Menggunakan</h3>
                        <div class="space-y-2 sm:space-y-3">
                            @php
                                $cvSteps = [
                                    ['icon' => 'fa-upload', 'title' => 'Buka Menu "Analisis CV"', 'desc' => 'Klik menu <strong>Analisis CV</strong> di sidebar. Anda akan melihat zona unggah dokumen di halaman utama.'],
                                    ['icon' => 'fa-file-arrow-up', 'title' => 'Unggah File CV Anda', 'desc' => 'Seret file CV (format <strong>PDF</strong>) ke dalam zona unggah, atau klik untuk memilih file dari perangkat Anda. Ukuran maksimal file adalah <strong>5 MB</strong>.'],
                                    ['icon' => 'fa-microchip', 'title' => 'Proses Analisis Otomatis', 'desc' => 'Setelah file diunggah, klik <strong>Analisis CV Saya</strong>. Sistem akan membaca isi CV dan mencocokkan kata kunci dengan database keahlian kami secara otomatis.'],
                                    ['icon' => 'fa-chart-bar', 'title' => 'Lihat Hasil Analisis', 'desc' => 'Hasilnya berupa daftar <strong>keahlian terdeteksi</strong> (lengkap dengan level), <strong>minat profesional</strong>, dan <strong>analisis kecocokan karir</strong> beserta rekomendasi lowongan yang sesuai.'],
                                    ['icon' => 'fa-user-circle', 'title' => 'Simpan ke Profil', 'desc' => 'Semua keahlian yang terdeteksi otomatis tersimpan di profil Anda. Anda bisa melihatnya kembali di halaman <strong>Pengaturan Profil</strong> pada bagian "Keahlian Terdeteksi".'],
                                ];
                            @endphp
                            @foreach($cvSteps as $i => $step)
                            <div class="flex gap-3 sm:gap-4 p-3 sm:p-4 rounded-xl bg-slate-800/50 border border-slate-700/50">
                                <div class="w-7 h-7 sm:w-8 sm:h-8 shrink-0 rounded-lg bg-blue-500/20 flex items-center justify-center text-blue-400 text-xs sm:text-sm font-extrabold">
                                    {{ $i + 1 }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs sm:text-sm font-bold text-white mb-0.5 sm:mb-1"><i class="fas {{ $step['icon'] }} text-blue-400 mr-1.5 hidden sm:inline"></i>{{ $step['title'] }}</p>
                                    <p class="text-[11px] sm:text-xs text-slate-400 leading-relaxed">{!! $step['desc'] !!}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex gap-3 sm:gap-4 p-3 sm:p-4 rounded-xl bg-amber-500/5 border border-amber-500/20">
                        <div class="w-9 h-9 sm:w-10 sm:h-10 shrink-0 bg-amber-500/20 rounded-xl flex items-center justify-center text-amber-400">
                            <i class="fas fa-lightbulb"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="font-semibold text-white text-xs sm:text-sm mb-1">💡 Tips Pro</p>
                            <p class="text-[11px] sm:text-xs text-slate-400 leading-relaxed">
                                Gunakan CV yang mengandung banyak <strong class="text-slate-300">kata kunci keahlian teknis</strong> (misalnya nama bahasa pemrograman, tools, sertifikasi) agar sistem bisa mendeteksi lebih banyak keahlian. Jika ingin reset dan unggah CV baru, Anda bisa melakukannya di halaman <strong class="text-slate-300">Pengaturan Profil</strong>.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════════
         SECTION 3: JELAJAHI LOWONGAN
    ══════════════════════════════════════════════════════════════════════════ --}}
    <div id="section-jobs" class="scroll-mt-24" data-aos="fade-up" data-aos-delay="250">
        <div class="glass-dark rounded-2xl overflow-hidden border border-emerald-500/20 hover:border-emerald-500/40 transition-colors">
            <button @click="activeSection = activeSection === 'jobs' ? null : 'jobs'"
                    class="w-full flex items-center justify-between px-4 sm:px-8 py-4 sm:py-6 text-left group">
                <div class="flex items-center gap-3 sm:gap-4">
                    <div class="w-11 h-11 sm:w-14 sm:h-14 shrink-0 rounded-xl sm:rounded-2xl bg-emerald-500/20 border border-emerald-500/30 flex items-center justify-center text-emerald-400 text-xl sm:text-2xl shadow-lg shadow-emerald-500/10">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <div class="min-w-0">
                        <h2 class="text-base sm:text-xl font-bold text-white group-hover:text-emerald-400 transition-colors">Jelajahi Lowongan</h2>
                        <p class="text-[10px] sm:text-xs text-slate-500 mt-0.5 truncate">Temukan lowongan kerja yang sesuai profil Anda</p>
                    </div>
                </div>
                <i class="fas fa-chevron-down text-slate-500 transition-transform duration-300 ml-2 shrink-0"
                   :class="activeSection === 'jobs' ? 'rotate-180 text-emerald-400' : ''"></i>
            </button>

            <div x-show="activeSection === 'jobs'"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 -translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-4"
                 style="display:none">
                <div class="px-4 sm:px-8 pb-6 sm:pb-8 space-y-4 sm:space-y-6">
                    <div class="h-px bg-gradient-to-r from-transparent via-emerald-500/30 to-transparent"></div>

                    <div>
                        <h3 class="text-xs sm:text-sm font-bold text-emerald-400 uppercase tracking-widest mb-2 sm:mb-3"><i class="fas fa-circle-info mr-2"></i>Apa Itu Jelajahi Lowongan?</h3>
                        <p class="text-slate-300 text-xs sm:text-sm leading-relaxed">
                            Fitur Jelajahi Lowongan memungkinkan Anda menelusuri seluruh database lowongan pekerjaan yang tersedia di platform CareerPredict. Setiap lowongan dilengkapi dengan informasi perusahaan, lokasi, kisaran gaji, jenis pekerjaan, deskripsi lengkap, dan persyaratan. Anda juga bisa menyimpan lowongan yang diminati ke <strong class="text-white">Pelacak Lamaran</strong> untuk di-follow up nanti.
                        </p>
                    </div>

                    <div>
                        <h3 class="text-xs sm:text-sm font-bold text-emerald-400 uppercase tracking-widest mb-2 sm:mb-3"><i class="fas fa-list-ol mr-2"></i>Cara Menggunakan</h3>
                        <div class="space-y-2 sm:space-y-3">
                            @php
                                $jobSteps = [
                                    ['icon' => 'fa-search', 'title' => 'Buka Menu "Jelajahi Lowongan"', 'desc' => 'Klik menu <strong>Jelajahi Lowongan</strong> di sidebar. Anda akan melihat daftar lowongan kerja dalam format kartu yang rapi.'],
                                    ['icon' => 'fa-filter', 'title' => 'Gunakan Filter Pencarian', 'desc' => 'Gunakan kolom pencarian di bagian atas untuk mencari berdasarkan <strong>judul, perusahaan, atau kata kunci</strong>. Anda juga bisa memfilter berdasarkan <strong>kategori</strong> dan <strong>lokasi</strong>.'],
                                    ['icon' => 'fa-eye', 'title' => 'Lihat Detail Lowongan', 'desc' => 'Klik tombol <strong>Lihat Detail</strong> pada kartu lowongan untuk membuka halaman detail yang berisi deskripsi lengkap, persyaratan, informasi gaji, dan lowongan serupa.'],
                                    ['icon' => 'fa-bookmark', 'title' => 'Simpan ke Pelacak', 'desc' => 'Klik ikon <strong>bookmark</strong> (🔖) pada kartu lowongan, atau klik tombol <strong>Simpan ke Pelacak</strong> di halaman detail. Lowongan akan masuk ke papan Kanban Pelacak Lamaran Anda.'],
                                    ['icon' => 'fa-external-link-alt', 'title' => 'Lamar Langsung', 'desc' => 'Di halaman detail lowongan, klik tombol <strong>Lamar Sekarang</strong> untuk diarahkan ke halaman lamaran asli perusahaan tersebut (jika tersedia).'],
                                ];
                            @endphp
                            @foreach($jobSteps as $i => $step)
                            <div class="flex gap-3 sm:gap-4 p-3 sm:p-4 rounded-xl bg-slate-800/50 border border-slate-700/50">
                                <div class="w-7 h-7 sm:w-8 sm:h-8 shrink-0 rounded-lg bg-emerald-500/20 flex items-center justify-center text-emerald-400 text-xs sm:text-sm font-extrabold">
                                    {{ $i + 1 }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs sm:text-sm font-bold text-white mb-0.5 sm:mb-1"><i class="fas {{ $step['icon'] }} text-emerald-400 mr-1.5 hidden sm:inline"></i>{{ $step['title'] }}</p>
                                    <p class="text-[11px] sm:text-xs text-slate-400 leading-relaxed">{!! $step['desc'] !!}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex gap-3 sm:gap-4 p-3 sm:p-4 rounded-xl bg-amber-500/5 border border-amber-500/20">
                        <div class="w-9 h-9 sm:w-10 sm:h-10 shrink-0 bg-amber-500/20 rounded-xl flex items-center justify-center text-amber-400">
                            <i class="fas fa-lightbulb"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="font-semibold text-white text-xs sm:text-sm mb-1">💡 Tips Pro</p>
                            <p class="text-[11px] sm:text-xs text-slate-400 leading-relaxed">
                                Cek halaman <strong class="text-slate-300">Info Gaji</strong> untuk melihat kisaran gaji rata-rata per kategori pekerjaan sebelum melamar. Ini akan membantu Anda bernegosiasi gaji dengan lebih percaya diri!
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════════
         SECTION 4: PELACAK LAMARAN
    ══════════════════════════════════════════════════════════════════════════ --}}
    <div id="section-tracker" class="scroll-mt-24" data-aos="fade-up" data-aos-delay="300">
        <div class="glass-dark rounded-2xl overflow-hidden border border-amber-500/20 hover:border-amber-500/40 transition-colors">
            <button @click="activeSection = activeSection === 'tracker' ? null : 'tracker'"
                    class="w-full flex items-center justify-between px-4 sm:px-8 py-4 sm:py-6 text-left group">
                <div class="flex items-center gap-3 sm:gap-4">
                    <div class="w-11 h-11 sm:w-14 sm:h-14 shrink-0 rounded-xl sm:rounded-2xl bg-amber-500/20 border border-amber-500/30 flex items-center justify-center text-amber-400 text-xl sm:text-2xl shadow-lg shadow-amber-500/10">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <div class="min-w-0">
                        <h2 class="text-base sm:text-xl font-bold text-white group-hover:text-amber-400 transition-colors">Pelacak Lamaran</h2>
                        <p class="text-[10px] sm:text-xs text-slate-500 mt-0.5 truncate">Papan Kanban untuk melacak status lamaran Anda</p>
                    </div>
                </div>
                <i class="fas fa-chevron-down text-slate-500 transition-transform duration-300 ml-2 shrink-0"
                   :class="activeSection === 'tracker' ? 'rotate-180 text-amber-400' : ''"></i>
            </button>

            <div x-show="activeSection === 'tracker'"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 -translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-4"
                 style="display:none">
                <div class="px-4 sm:px-8 pb-6 sm:pb-8 space-y-4 sm:space-y-6">
                    <div class="h-px bg-gradient-to-r from-transparent via-amber-500/30 to-transparent"></div>

                    <div>
                        <h3 class="text-xs sm:text-sm font-bold text-amber-400 uppercase tracking-widest mb-2 sm:mb-3"><i class="fas fa-circle-info mr-2"></i>Apa Itu Pelacak Lamaran?</h3>
                        <p class="text-slate-300 text-xs sm:text-sm leading-relaxed">
                            Pelacak Lamaran adalah fitur manajemen lamaran kerja berbasis <strong class="text-white">papan Kanban</strong> — sebuah metode visual yang populer digunakan oleh profesional dan perusahaan di seluruh dunia. Fitur ini memungkinkan Anda menyimpan lowongan yang diminati dan melacak progres lamaran melalui 5 tahap: <strong class="text-white">Minat → Dilamar → Wawancara → Ditawarkan → Ditolak</strong>.
                        </p>
                    </div>

                    <div>
                        <h3 class="text-xs sm:text-sm font-bold text-amber-400 uppercase tracking-widest mb-2 sm:mb-3"><i class="fas fa-list-ol mr-2"></i>Cara Menggunakan</h3>
                        <div class="space-y-2 sm:space-y-3">
                            @php
                                $trackerSteps = [
                                    ['icon' => 'fa-bookmark', 'title' => 'Simpan Lowongan ke Pelacak', 'desc' => 'Dari halaman <strong>Jelajahi Lowongan</strong>, klik ikon bookmark pada kartu lowongan atau tombol <strong>Simpan ke Pelacak</strong> di halaman detail. Lowongan akan masuk ke kolom <strong>Minat</strong>.'],
                                    ['icon' => 'fa-columns', 'title' => 'Buka Papan Kanban', 'desc' => 'Klik menu <strong>Pelacak Lamaran</strong> di sidebar. Anda akan melihat 5 kolom status: <strong>Minat</strong>, <strong>Dilamar</strong>, <strong>Wawancara</strong>, <strong>Ditawarkan</strong>, dan <strong>Ditolak</strong>.'],
                                    ['icon' => 'fa-hand-pointer', 'title' => 'Seret & Lepas Kartu', 'desc' => 'Untuk mengubah status lamaran, cukup <strong>seret (drag)</strong> kartu lowongan dari satu kolom ke kolom lainnya. Misalnya: seret dari kolom "Minat" ke "Dilamar" setelah Anda mengirim lamaran.'],
                                    ['icon' => 'fa-ellipsis-v', 'title' => 'Kelola Kartu', 'desc' => 'Klik ikon <strong>titik tiga (⋮)</strong> pada setiap kartu untuk melihat detail lowongan atau menghapus kartu dari pelacak jika sudah tidak diperlukan.'],
                                    ['icon' => 'fa-chart-pie', 'title' => 'Pantau Statistik', 'desc' => 'Di bagian atas papan, Anda bisa melihat <strong>statistik ringkasan</strong>: total lamaran, jumlah per kolom status, sehingga Anda tahu progres perjalanan karir secara keseluruhan.'],
                                ];
                            @endphp
                            @foreach($trackerSteps as $i => $step)
                            <div class="flex gap-3 sm:gap-4 p-3 sm:p-4 rounded-xl bg-slate-800/50 border border-slate-700/50">
                                <div class="w-7 h-7 sm:w-8 sm:h-8 shrink-0 rounded-lg bg-amber-500/20 flex items-center justify-center text-amber-400 text-xs sm:text-sm font-extrabold">
                                    {{ $i + 1 }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs sm:text-sm font-bold text-white mb-0.5 sm:mb-1"><i class="fas {{ $step['icon'] }} text-amber-400 mr-1.5 hidden sm:inline"></i>{{ $step['title'] }}</p>
                                    <p class="text-[11px] sm:text-xs text-slate-400 leading-relaxed">{!! $step['desc'] !!}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex gap-3 sm:gap-4 p-3 sm:p-4 rounded-xl bg-amber-500/5 border border-amber-500/20">
                        <div class="w-9 h-9 sm:w-10 sm:h-10 shrink-0 bg-amber-500/20 rounded-xl flex items-center justify-center text-amber-400">
                            <i class="fas fa-lightbulb"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="font-semibold text-white text-xs sm:text-sm mb-1">💡 Tips Pro</p>
                            <p class="text-[11px] sm:text-xs text-slate-400 leading-relaxed">
                                Gunakan fitur seret & lepas (drag and drop) untuk dengan cepat memperbarui status lamaran Anda. Status akan tersimpan secara otomatis ke database tanpa perlu menekan tombol simpan!
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── CTA Bottom ─────────────────────────────────────────────────────────── --}}
    <div class="glass rounded-2xl p-5 sm:p-8 text-center" data-aos="fade-up">
        <div class="w-12 h-12 sm:w-16 sm:h-16 mx-auto bg-blue-600/20 rounded-2xl flex items-center justify-center text-blue-400 text-2xl sm:text-3xl mb-3 sm:mb-4">
            <i class="fas fa-rocket"></i>
        </div>
        <h3 class="text-lg sm:text-xl font-bold text-white mb-2">Siap Memulai Perjalanan Karir Anda?</h3>
        <p class="text-slate-400 text-xs sm:text-sm mb-4 sm:mb-6 max-w-md mx-auto">Mulai dari Tes DNA Karir untuk mengetahui potensi tersembunyi Anda, lalu jelajahi lowongan yang sesuai!</p>
        <div class="flex flex-col sm:flex-row flex-wrap justify-center gap-2 sm:gap-3">
            <a href="{{ route('assessment.index') }}" class="btn-premium px-5 sm:px-6 py-2.5 sm:py-3 text-xs sm:text-sm">
                <i class="fas fa-wand-magic-sparkles mr-2"></i> Mulai Tes DNA Karir
            </a>
            <a href="{{ route('jobs.index') }}" class="px-5 sm:px-6 py-2.5 sm:py-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs sm:text-sm font-bold transition-all text-center">
                <i class="fas fa-briefcase mr-2"></i> Jelajahi Lowongan
            </a>
        </div>
    </div>

</div>
@endsection
