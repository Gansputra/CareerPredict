@extends('layouts.app')

@section('title', 'Hasil Analisis CV')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-white mb-1">Laporan Analisis <span class="text-gradient">CV</span></h1>
            <p class="text-slate-400 text-sm">Laporan inteligensi karir personal Anda yang didukung analisis Certainty Factor.</p>
        </div>
        <a href="{{ route('cv.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-sm transition-all shrink-0">
            <i class="fas fa-redo mr-2"></i> Analisis CV Lain
        </a>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="glass-dark p-5 animate-fade-in" style="animation-delay: 0ms">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 bg-blue-600/10 rounded-xl flex items-center justify-center">
                    <i class="fas fa-code text-blue-400 text-sm"></i>
                </div>
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Keahlian Ditemukan</span>
            </div>
            <p class="text-3xl font-extrabold text-white">{{ count($detectedSkills) }}</p>
        </div>
        <div class="glass-dark p-5 animate-fade-in" style="animation-delay: 80ms">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 bg-purple-600/10 rounded-xl flex items-center justify-center">
                    <i class="fas fa-heart text-purple-400 text-sm"></i>
                </div>
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Minat</span>
            </div>
            <p class="text-3xl font-extrabold text-white">{{ count($detectedInterests) }}</p>
        </div>
        <div class="glass-dark p-5 animate-fade-in" style="animation-delay: 160ms">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 bg-emerald-600/10 rounded-xl flex items-center justify-center">
                    <i class="fas fa-briefcase text-emerald-400 text-sm"></i>
                </div>
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Kecocokan Lowongan</span>
            </div>
            <p class="text-3xl font-extrabold text-white">{{ count($recommendations) }}</p>
        </div>
        <div class="glass-dark p-5 animate-fade-in" style="animation-delay: 240ms">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 bg-amber-600/10 rounded-xl flex items-center justify-center">
                    <i class="fas fa-file-lines text-amber-400 text-sm"></i>
                </div>
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Kata Dipindai</span>
            </div>
            <p class="text-3xl font-extrabold text-white">{{ number_format($cvInfo['word_count']) }}</p>
        </div>
    </div>

    <!-- Deep AI Model Classification -->
    <div class="glass-dark p-6 lg:p-8 animate-fade-in" style="animation-delay: 300ms">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-500/10 border border-purple-500/20 text-purple-400 text-[10px] font-bold uppercase tracking-wider mb-2">
                    <i class="fas fa-brain"></i> Neural Network Prediction
                </div>
                <h3 class="text-xl font-bold text-white">Klasifikasi Karir Deep AI</h3>
                <p class="text-xs text-slate-400">Prediksi kategori karir utama berdasarkan analisis model Keras/TensorFlow.js.</p>
            </div>
            <div class="flex items-center gap-2 text-xs text-slate-500">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Model TF.js Aktif (Client-Side)</span>
            </div>
        </div>

        <!-- Skeleton Loading State -->
        <div id="ai-loading" class="py-8 text-center space-y-4">
            <div class="relative w-16 h-16 mx-auto">
                <div class="absolute inset-0 rounded-full border-4 border-purple-500/20"></div>
                <div class="absolute inset-0 rounded-full border-4 border-t-purple-500 animate-spin"></div>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-300">Menghitung Vektor TF-IDF & Inference Model...</p>
                <p class="text-[10px] text-slate-500 mt-1">Proses pemrosesan bahasa alami (NLP) sedang berlangsung di browser Anda</p>
            </div>
        </div>

        <!-- Error State -->
        <div id="ai-error" class="hidden py-8 text-center text-red-400 space-y-2">
            <i class="fas fa-exclamation-triangle text-3xl"></i>
            <p class="text-sm font-medium">Gagal menjalankan model AI. Menggunakan fallback analisis kata kunci.</p>
        </div>

        <!-- Success State -->
        <div id="ai-success" class="hidden space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-center">
                <!-- Top 1 Prediction Big Card -->
                <div class="md:col-span-5 p-6 rounded-2xl bg-gradient-to-br from-purple-900/30 to-blue-900/30 border border-purple-500/20 flex flex-col justify-between h-full relative overflow-hidden">
                    <div class="absolute -right-10 -bottom-10 w-32 h-32 bg-purple-500/10 rounded-full blur-2xl"></div>
                    <div>
                        <span class="text-[9px] font-bold text-purple-400 uppercase tracking-widest">Prediksi Tertinggi</span>
                        <h4 id="top-label" class="text-2xl font-extrabold text-white mt-1 mb-2">-</h4>
                        <p id="top-friendly-name" class="text-xs text-slate-300 font-medium">-</p>
                    </div>
                    <div class="mt-8">
                        <div class="flex justify-between items-baseline mb-2">
                            <span class="text-slate-400 text-xs">Skor Keyakinan (Confidence)</span>
                            <span id="top-confidence" class="text-2xl font-black text-purple-400">0%</span>
                        </div>
                        <div class="w-full h-2.5 bg-slate-800 rounded-full overflow-hidden">
                            <div id="top-progress-bar" class="h-full bg-gradient-to-r from-purple-500 to-blue-500 rounded-full transition-all duration-1000" style="width: 0%"></div>
                        </div>
                    </div>
                </div>

                <!-- Runner Ups -->
                <div class="md:col-span-7 space-y-4">
                    <h5 class="text-xs font-bold text-slate-500 uppercase tracking-widest">Kategori Alternatif Lainnya</h5>
                    <div id="alternative-list" class="space-y-3">
                        <!-- Alternatif list will be generated here -->
                    </div>
                </div>
            </div>
            
            <div class="p-4 rounded-xl bg-purple-500/5 border border-purple-500/10 text-xs text-slate-300 flex items-start gap-2.5">
                <i class="fas fa-info-circle text-purple-400 mt-0.5"></i>
                <p class="leading-relaxed">
                    Hasil analisis di atas diperoleh langsung dari model saraf tiruan (neural network) berbobot 10,8 MB yang berjalan secara lokal di browser Anda. Model membandingkan struktur kalimat dan istilah spesifik pada CV Anda dengan dataset pelatihan berisi ribuan CV profesional.
                </p>
            </div>
        </div>
    </div>

    <!-- Skills & Career Fit -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Detected Skills -->
        <div class="glass-dark p-6 lg:p-8">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-white"><i class="fas fa-microchip text-blue-400 mr-2"></i> Keahlian Terdeteksi</h3>
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ count($detectedSkills) }} ditemukan</span>
            </div>
            @if(count($detectedSkills) > 0)
            <div class="space-y-3">
                @foreach($detectedSkills as $skill)
                <div class="flex items-center justify-between p-3 rounded-xl bg-slate-800/50 hover:bg-slate-800 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 rounded-full {{ $skill['confidence'] >= 0.8 ? 'bg-emerald-400' : ($skill['confidence'] >= 0.7 ? 'bg-blue-400' : 'bg-amber-400') }}"></div>
                        <span class="text-sm font-medium text-white">{{ $skill['name'] }}</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-20 h-1.5 bg-slate-700 rounded-full overflow-hidden hidden sm:block">
                            <div class="h-full rounded-full {{ $skill['confidence'] >= 0.8 ? 'bg-emerald-400' : ($skill['confidence'] >= 0.7 ? 'bg-blue-400' : 'bg-amber-400') }}"
                                 style="width: {{ $skill['confidence'] * 100 }}%"></div>
                        </div>
                        <span class="text-xs font-bold {{ $skill['confidence'] >= 0.8 ? 'text-emerald-400' : ($skill['confidence'] >= 0.7 ? 'text-blue-400' : 'text-amber-400') }}">
                            {{ round($skill['confidence'] * 100) }}%
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-8">
                <i class="fas fa-search text-slate-700 text-3xl mb-3"></i>
                <p class="text-slate-500 text-sm">Tidak ada keahlian terdeteksi. Coba CV yang lebih detail.</p>
            </div>
            @endif
        </div>

        <!-- Career Category Fit -->
        <div class="glass-dark p-6 lg:p-8">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-white"><i class="fas fa-chart-pie text-purple-400 mr-2"></i> Analisis Kecocokan Karir</h3>
            </div>
            @if(count($careerCategories) > 0)
            <div class="space-y-4">
                @foreach($careerCategories as $cat)
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-white">{{ $cat['name'] }}</span>
                        <span class="text-xs font-bold {{ $cat['score'] >= 50 ? 'text-emerald-400' : ($cat['score'] >= 25 ? 'text-blue-400' : 'text-slate-400') }}">
                            {{ $cat['score'] }}% cocok
                        </span>
                    </div>
                    <div class="w-full h-2.5 bg-slate-800 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-1000 {{ $cat['score'] >= 50 ? 'bg-gradient-to-r from-emerald-500 to-teal-400' : ($cat['score'] >= 25 ? 'bg-gradient-to-r from-blue-500 to-indigo-400' : 'bg-slate-600') }}"
                             style="width: {{ $cat['score'] }}%"></div>
                    </div>
                    <p class="text-[10px] text-slate-500 mt-1">{{ $cat['matched'] }} dari {{ $cat['total'] }} keahlian kunci cocok</p>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-8">
                <i class="fas fa-chart-bar text-slate-700 text-3xl mb-3"></i>
                <p class="text-slate-500 text-sm">Data tidak cukup untuk analisis karir.</p>
            </div>
            @endif

            @if(count($detectedInterests) > 0)
            <div class="mt-8 pt-6 border-t border-slate-800">
                <h4 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-3">Minat Terdeteksi</h4>
                <div class="flex flex-wrap gap-2">
                    @foreach($detectedInterests as $interest)
                    <span class="px-3 py-1.5 rounded-full bg-purple-500/10 text-purple-400 text-xs font-bold">{{ $interest['name'] }}</span>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Job Recommendations -->
    <div>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-white">Lowongan yang Direkomendasikan</h2>
                <p class="text-slate-400 text-sm">Berdasarkan analisis CV Anda, diurutkan berdasarkan skor Certainty Factor.</p>
            </div>
        </div>

        @if(count($recommendations) > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($recommendations as $rec)
            <div class="job-rec-card glass-dark overflow-hidden flex flex-col card-hover border-t-4 animate-fade-in {{ $loop->first ? 'border-blue-600 shadow-2xl shadow-blue-600/10' : ($loop->index < 3 ? 'border-indigo-500/50' : 'border-slate-700') }}"
                 data-category-slug="{{ $rec['job']->category->slug ?? '' }}"
                 style="animation-delay: {{ $loop->index * 60 }}ms">

                @if($loop->index < 3)
                <div class="px-4 py-2 bg-gradient-to-r {{ $loop->first ? 'from-blue-600 to-indigo-600' : ($loop->index === 1 ? 'from-indigo-600/50 to-purple-600/50' : 'from-slate-700 to-slate-600') }} flex items-center justify-between">
                    <span class="text-[10px] font-bold text-white uppercase tracking-widest">
                        <i class="fas {{ $loop->first ? 'fa-trophy' : ($loop->index === 1 ? 'fa-medal' : 'fa-award') }} mr-1"></i>
                        #{{ $loop->iteration }} Kecocokan Terbaik
                    </span>
                    <span class="text-xs font-bold text-white/80">CF {{ $rec['score'] }}</span>
                </div>
                @endif

                <div class="p-6 flex-1">
                    <div class="flex items-center justify-between mb-4">
                        <span class="px-2.5 py-1 rounded-full bg-blue-600/10 text-blue-500 text-[10px] font-bold uppercase tracking-widest">{{ $rec['job']->category->name ?? 'Umum' }}</span>
                        <div class="ai-badge-placeholder"></div>
                        <div class="text-right">
                            <span class="text-xl font-bold text-white">{{ $rec['confidence'] }}%</span>
                            <p class="text-[8px] text-slate-500 uppercase font-bold">Kecocokan</p>
                        </div>
                    </div>

                    <div class="w-full h-1 bg-slate-800 rounded-full overflow-hidden mb-4">
                        <div class="h-full rounded-full {{ $rec['confidence'] >= 60 ? 'bg-emerald-500' : ($rec['confidence'] >= 30 ? 'bg-blue-500' : 'bg-amber-500') }}"
                             style="width: {{ min($rec['confidence'], 100) }}%"></div>
                    </div>

                    <h3 class="text-lg font-bold text-white mb-1">{{ $rec['job']->title }}</h3>
                    <p class="text-sm text-blue-400 mb-3"><i class="fas fa-building mr-1"></i> {{ $rec['job']->company_name }}</p>

                    <!-- Matched Skills Tags -->
                    @if(count($rec['matched_skills']) > 0)
                    <div class="flex flex-wrap gap-1.5 mb-4">
                        @foreach($rec['matched_skills'] as $ms)
                        <span class="px-2 py-0.5 rounded-md bg-emerald-500/10 text-emerald-400 text-[10px] font-bold">{{ $ms }}</span>
                        @endforeach
                    </div>
                    @endif

                    <div class="p-3 rounded-xl bg-slate-800/50 border border-slate-700/50">
                        <p class="text-xs text-slate-300 leading-relaxed">{{ $rec['explanation'] }}</p>
                    </div>
                </div>

                <div class="p-4 bg-slate-800/30 border-t border-slate-800 flex items-center justify-between">
                    <span class="text-xs text-slate-500"><i class="fas fa-map-marker-alt mr-1"></i> {{ $rec['job']->location }}</span>
                    <a href="{{ route('jobs.show', $rec['job']->slug) }}" class="text-sm font-bold text-blue-500 hover:text-blue-400 transition-colors">
                        Lihat <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="glass-dark p-16 text-center">
            <div class="w-20 h-20 bg-slate-800 rounded-3xl flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-search text-slate-600 text-3xl"></i>
            </div>
            <h3 class="text-xl font-bold text-white mb-3">Tidak Ada Kecocokan Kuat</h3>
            <p class="text-slate-400 max-w-md mx-auto mb-6">CV Anda tidak menghasilkan kecocokan kuat dengan lowongan saat ini. Coba unggah CV yang lebih detail atau jelajahi lowongan secara manual.</p>
            <a href="{{ route('jobs.index') }}" class="btn-premium px-8">Jelajahi Semua Lowongan</a>
        </div>
        @endif
    </div>

    <!-- Bottom CTA -->
    <div class="glass p-6 sm:p-8 flex flex-col sm:flex-row items-center justify-between gap-6">
        <div>
            <h3 class="text-lg font-bold text-white mb-1">Ingin analisis lebih dalam?</h3>
            <p class="text-slate-400 text-sm">Ambil Tes DNA Karir lengkap untuk analisis komprehensif berbasis kepribadian.</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('cv.index') }}" class="px-5 py-3 rounded-xl bg-slate-800 hover:bg-slate-700 transition-all text-white font-bold text-sm">
                <i class="fas fa-upload mr-2"></i> Unggah Ulang
            </a>
            <a href="{{ route('assessment.index') }}" class="btn-premium px-5 py-3 text-sm">
                <i class="fas fa-dna mr-2"></i> Tes DNA Karir
            </a>
        </div>
    </div>
</div>

@push('scripts')
<!-- Load TensorFlow.js via jsDelivr CDN -->
<script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@4.22.0/dist/tf.min.js"></script>

<script>
async function startAIPrediction() {
    const rawCvText = @json($rawText ?? '');
    
    if (!rawCvText || rawCvText.trim().length < 50) {
        document.getElementById('ai-loading').classList.add('hidden');
        document.getElementById('ai-error').classList.remove('hidden');
        return;
    }

    // Friendly names mapping
    const labelFriendlyNames = {
        "ACCOUNTANT": "Akuntan / Keuangan",
        "ADVOCATE": "Advokat / Hukum",
        "AGRICULTURE": "Pertanian & Agronomi",
        "APPAREL": "Mode & Pakaian",
        "ARTS": "Seni & Industri Kreatif",
        "AUTOMOBILE": "Teknik Otomotif",
        "AVIATION": "Penerbangan / Dirgantara",
        "BANKING": "Perbankan / Layanan Finansial",
        "BPO": "BPO & Customer Service",
        "BUSINESS-DEVELOPMENT": "Pengembangan Bisnis (BizDev)",
        "CHEF": "Kulinari & Tata Boga",
        "CONSTRUCTION": "Konstruksi / Sipil",
        "CONSULTANT": "Konsultan Bisnis",
        "DESIGNER": "Desain Grafis / UI/UX",
        "DIGITAL-MEDIA": "Media Digital & Periklanan",
        "ENGINEERING": "Rekayasa & Teknik Umum",
        "FINANCE": "Keuangan & Analis Finansial",
        "FITNESS": "Kebugaran & Kesehatan",
        "HEALTHCARE": "Layanan Kesehatan & Medis",
        "HR": "Sumber Daya Manusia (HRD)",
        "INFORMATION-TECHNOLOGY": "Teknologi Informasi & Software",
        "PUBLIC-RELATIONS": "Hubungan Masyarakat (PR)",
        "SALES": "Penjualan & Pemasaran",
        "TEACHER": "Pendidik & Guru / Akademisi"
    };

    // Label to database category mapping (by slug)
    const labelToDbCategory = {
        "INFORMATION-TECHNOLOGY": "teknologi",
        "DESIGNER": "desain-kreatif",
        "DIGITAL-MEDIA": "desain-kreatif",
        "FINANCE": "keuangan",
        "ACCOUNTANT": "keuangan",
        "BANKING": "keuangan",
        "HEALTHCARE": "kesehatan",
        "FITNESS": "kesehatan",
        "TEACHER": "pendidikan",
        "HR": "sumber-daya-manusia",
        "SALES": "penjualan",
        "BUSINESS-DEVELOPMENT": "penjualan",
        "ENGINEERING": "rekayasa-teknik",
        "AUTOMOBILE": "rekayasa-teknik",
        "AVIATION": "rekayasa-teknik",
        "CONSTRUCTION": "rekayasa-teknik",
        "AGRICULTURE": "rekayasa-teknik",
        "ADVOCATE": "manajemen",
        "CONSULTANT": "manajemen",
        "BPO": "manajemen",
        "CHEF": "desain-kreatif",
        "APPAREL": "desain-kreatif",
        "ARTS": "desain-kreatif"
    };

    const stopWords = new Set(["a", "about", "above", "across", "after", "afterwards", "again", "against", "all", "almost", "alone", "along", "already", "also", "although", "always", "am", "among", "amongst", "amoungst", "amount", "an", "and", "another", "any", "anyhow", "anyone", "anything", "anyway", "anyways", "anywhere", "are", "around", "as", "at", "back", "be", "became", "because", "become", "becomes", "becoming", "been", "before", "beforehand", "behind", "being", "below", "beside", "besides", "between", "beyond", "bill", "both", "bottom", "but", "by", "call", "can", "cannot", "cant", "co", "con", "could", "couldnt", "cry", "de", "describe", "detail", "do", "done", "down", "due", "during", "each", "eg", "eight", "either", "eleven", "else", "elsewhere", "empty", "enough", "etc", "even", "ever", "every", "everyone", "everything", "everywhere", "except", "few", "fifteen", "fifty", "fill", "find", "fire", "first", "five", "for", "former", "formerly", "forty", "found", "four", "from", "front", "full", "further", "get", "give", "go", "had", "has", "hasnt", "have", "he", "hence", "her", "here", "hereafter", "hereby", "herein", "hereupon", "hers", "herself", "him", "himself", "his", "how", "however", "hundred", "i", "ie", "if", "in", "inc", "indeed", "interest", "into", "is", "it", "its", "itself", "keep", "last", "latter", "latterly", "least", "less", "ltd", "made", "many", "may", "me", "meanwhile", "might", "mill", "mine", "more", "moreover", "most", "mostly", "move", "much", "must", "my", "myself", "name", "namely", "neither", "never", "nevertheless", "next", "nine", "no", "nobody", "none", "noone", "nor", "not", "nothing", "now", "nowhere", "of", "off", "often", "on", "once", "one", "only", "onto", "or", "other", "others", "otherwise", "our", "ours", "ourselves", "out", "over", "own", "part", "per", "perhaps", "please", "put", "rather", "re", "same", "see", "seem", "seemed", "seeming", "seems", "serious", "several", "she", "should", "show", "side", "since", "sincere", "six", "sixty", "so", "some", "somehow", "someone", "something", "sometime", "sometimes", "somewhere", "still", "such", "system", "take", "ten", "than", "that", "the", "their", "them", "themselves", "then", "thence", "there", "thereafter", "thereby", "therefore", "therein", "thereupon", "these", "they", "thick", "thin", "third", "this", "those", "though", "three", "through", "throughout", "thru", "thus", "to", "together", "too", "top", "toward", "towards", "twelve", "twenty", "two", "un", "under", "until", "up", "upon", "us", "very", "via", "was", "we", "well", "were", "what", "whatever", "when", "whence", "whenever", "where", "whereafter", "whereas", "whereby", "wherein", "whereupon", "wherever", "whether", "which", "while", "whither", "who", "whoever", "whole", "whom", "whose", "why", "will", "with", "within", "without", "would", "yet", "you", "your", "yours", "yourself", "yourselves"]);

    function translateIndoToEng(text) {
        if (!text || typeof text !== 'string') return "";
        const dictionary = [
            // IT / Software / Programming
            [/\bmengembangkan\b/g, 'develop'],
            [/\bpengembang\b/g, 'developer'],
            [/\bpengembangan\b/g, 'development'],
            [/\bmemelihara\b/g, 'maintain'],
            [/\bpemeliharaan\b/g, 'maintenance'],
            [/\bfitur\b/g, 'feature'],
            [/\barsitektur\b/g, 'architecture'],
            [/\bmeningkatkan\b/g, 'improve'],
            [/\bperforma\b/g, 'performance'],
            [/\bkecepatan\b/g, 'speed'],
            [/\bteknik\b/g, 'technique'],
            [/\boptimasi\b/g, 'optimization'],
            [/\bkueri\b/g, 'query'],
            [/\bbasis data\b/g, 'database'],
            [/\bmerancang\b/g, 'design'],
            [/\bperancangan\b/g, 'design'],
            [/\bmengintegrasikan\b/g, 'integrate'],
            [/\bintegrasi\b/g, 'integration'],
            [/\bsistem\b/g, 'system'],
            [/\bmanajemen\b/g, 'management'],
            [/\bpengelolaan\b/g, 'management'],
            [/\bmengelola\b/g, 'manage'],
            [/\bberkolaborasi\b/g, 'collaborate'],
            [/\bkolaborasi\b/g, 'collaboration'],
            [/\bmembangun\b/g, 'build'],
            [/\bmembuat\b/g, 'create'],
            [/\bkustomisasi\b/g, 'customize'],
            [/\binventaris\b/g, 'inventory'],
            [/\bberbasis\b/g, 'based'],
            [/\bmenerapkan\b/g, 'apply'],
            [/\bautentikasi\b/g, 'authentication'],
            [/\baman\b/g, 'secure'],
            [/\bkeamanan\b/g, 'security'],
            [/\bmemproteksi\b/g, 'protect'],
            [/\bperlindungan\b/g, 'protection'],
            [/\bkesalahan\b/g, 'error'],
            [/\bpenulisan\b/g, 'writing'],
            [/\bdokumentasi\b/g, 'documentation'],
            [/\bteknis\b/g, 'technical'],
            [/\bkemudahan\b/g, 'ease'],
            [/\bkeahlian\b/g, 'skills'],
            [/\bjaringan\b/g, 'network'],
            [/\binformasi\b/g, 'information'],
            [/\bteknologi\b/g, 'technology'],
            [/\bperangkat lunak\b/g, 'software'],
            [/\bperangkat keras\b/g, 'hardware'],
            [/\brekayasa\b/g, 'engineering'],
            [/\bteknisi\b/g, 'engineer'],
            [/\bsolusi\b/g, 'solution'],
            [/\buji\b/g, 'test'],
            [/\bpengujian\b/g, 'testing'],
            
            // Business / Sales / Marketing
            [/\bpemasaran\b/g, 'marketing'],
            [/\bpenjualan\b/g, 'sales'],
            [/\bbisnis\b/g, 'business'],
            [/\blayanan\b/g, 'service'],
            [/\bpelanggan\b/g, 'customer'],
            [/\bkeuangan\b/g, 'finance'],
            [/\bakuntansi\b/g, 'accounting'],
            [/\bakuntan\b/g, 'accountant'],
            [/\bperbankan\b/g, 'banking'],
            [/\bsumber daya manusia\b/g, 'human resources'],
            [/\bhubungan masyarakat\b/g, 'public relations'],
            [/\bkomunikasi\b/g, 'communication'],
            
            // Healthcare / Medical
            [/\bkesehatan\b/g, 'health'],
            [/\bmedis\b/g, 'medical'],
            [/\blayanan kesehatan\b/g, 'healthcare'],
            [/\bkebugaran\b/g, 'fitness'],
            
            // Education / General
            [/\bpendidikan\b/g, 'education'],
            [/\bguru\b/g, 'teacher'],
            [/\bpendidik\b/g, 'educator'],
            [/\bpembelajaran\b/g, 'learning'],
            [/\bmengajar\b/g, 'teach'],
            
            // Common verbs / adverbs / prepositions
            [/\bmenggunakan\b/g, 'using'],
            [/\bmelakukan\b/g, 'doing'],
            [/\bkerja\b/g, 'work'],
            [/\bbekerja\b/g, 'work'],
            [/\bpekerjaan\b/g, 'job'],
            [/\bpengalaman\b/g, 'experience'],
            [/\butama\b/g, 'main'],
            [/\baktif\b/g, 'active'],
            [/\bsecara\b/g, 'in'],
            [/\bdengan\b/g, 'with'],
            [/\bdalam\b/g, 'in'],
            [/\buntuk\b/g, 'for'],
            [/\bdari\b/g, 'from'],
            [/\bpada\b/g, 'on'],
            [/\bsekarang\b/g, 'present'],
            [/\bserta\b/g, 'and'],
            [/\bdemi\b/g, 'for'],
            [/\batau\b/g, 'or']
        ];
        
        let translated = text.toLowerCase();
        for (const [pattern, replacement] of dictionary) {
            translated = translated.replace(pattern, replacement);
        }
        return translated;
    }

    function cleanText(text) {
        if (!text || typeof text !== 'string') return "";
        text = text.toLowerCase();
        // Remove URLs
        text = text.replace(/http\S+|www\S+/g, '');
        // Remove emails
        text = text.replace(/\S+@\S+/g, '');
        // Remove phone numbers
        text = text.replace(/[\+]?[\d\-\(\)\s]{7,}/g, ' ');
        // Remove special chars (matching Python's \w tokenization)
        text = text.replace(/[^a-z0-9\s]/g, ' ');
        // Extra whitespaces
        text = text.replace(/\s+/g, ' ').trim();
        return text;
    }

    try {
        // 1. Fetch metadata files parallelly
        const [labelMappingRes, tfidfVocabRes, tfidfIdfRes] = await Promise.all([
            fetch("{{ asset('ml-model/label_mapping.json') }}"),
            fetch("{{ asset('ml-model/tfidf_vocab.json') }}"),
            fetch("{{ asset('ml-model/tfidf_idf.json') }}")
        ]);

        if (!labelMappingRes.ok || !tfidfVocabRes.ok || !tfidfIdfRes.ok) {
            throw new Error("Failed to load model metadata.");
        }

        const labelMapping = await labelMappingRes.json();
        const tfidfVocab = await tfidfVocabRes.json();
        const tfidfIdf = await tfidfIdfRes.json();

        // 2. Preprocess text and filter stop words
        const translatedText = translateIndoToEng(rawCvText);
        const cleaned = cleanText(translatedText);
        const rawWords = cleaned.split(' ').filter(w => w.length >= 2);
        const words = rawWords.filter(w => !stopWords.has(w));
        
        // Generate term frequency (unigrams + bigrams)
        const termFreq = {};
        // Unigrams
        words.forEach(word => {
            termFreq[word] = (termFreq[word] || 0) + 1;
        });
        // Bigrams
        for (let i = 0; i < words.length - 1; i++) {
            const bigram = words[i] + ' ' + words[i+1];
            termFreq[bigram] = (termFreq[bigram] || 0) + 1;
        }

        // 3. Vectorization (L2 normalized TF-IDF)
        const vocabSize = 5000;
        const vector = new Float32Array(vocabSize);
        
        for (const term in termFreq) {
            if (tfidfVocab[term] !== undefined) {
                const idx = tfidfVocab[term];
                const count = termFreq[term];
                const idf = tfidfIdf[idx];
                vector[idx] = count * idf;
            }
        }

        // L2 Norm
        let sumSquares = 0;
        for (let i = 0; i < vector.length; i++) {
            sumSquares += vector[i] * vector[i];
        }
        const norm = Math.sqrt(sumSquares);
        if (norm > 0) {
            for (let i = 0; i < vector.length; i++) {
                vector[i] /= norm;
            }
        }

        // 4. Load Model and Predict
        const model = await tf.loadLayersModel("{{ asset('ml-model/tfjs_model/model.json') }}");
        const tensor = tf.tensor2d([Array.from(vector)], [1, vocabSize]);
        const prediction = model.predict(tensor);
        const scores = await prediction.data();

        // 5. Get top predictions with rule-based keyword boost for robustness
        const textLowerForBoost = rawCvText.toLowerCase();
        
        const boosts = {
            "INFORMATION-TECHNOLOGY": 0,
            "DESIGNER": 0,
            "ARTS": 0,
            "ACCOUNTANT": 0,
            "FINANCE": 0,
            "HR": 0,
            "TEACHER": 0,
            "SALES": 0,
            "HEALTHCARE": 0
        };
        
        // IT Keywords
        const itKeywords = ['developer', 'react', 'laravel', 'mysql', 'postgresql', 'node.js', 'javascript', 'programming', 'programmer', 'coding', 'full-stack', 'frontend', 'backend', 'web dev', 'software', 'php', 'database', 'api', 'scrum', 'git', 'postman', 'jwt'];
        let itMatches = 0;
        itKeywords.forEach(kw => {
            if (textLowerForBoost.includes(kw)) itMatches++;
        });
        if (itMatches >= 3) {
            boosts["INFORMATION-TECHNOLOGY"] = 0.5; // Strong boost for IT
        }
        
        // Designer Keywords
        const designKeywords = ['figma', 'ui/ux', 'photoshop', 'illustrator', 'graphic design', 'ui design', 'ux design', 'wireframe', 'prototyping'];
        let designMatches = 0;
        designKeywords.forEach(kw => {
            if (textLowerForBoost.includes(kw)) designMatches++;
        });
        if (designMatches >= 3 && !textLowerForBoost.includes('developer') && !textLowerForBoost.includes('coding')) {
            boosts["DESIGNER"] = 0.4;
        }

        const results = [];
        for (let i = 0; i < scores.length; i++) {
            const labelKey = i.toString();
            const label = labelMapping[labelKey] || labelKey;
            const finalScore = scores[i] + (boosts[label] || 0);
            results.push({
                label: label,
                score: finalScore
            });
        }
        results.sort((a, b) => b.score - a.score);

        // Normalize scores if the top score exceeds 1.0 (so confidence is capped at 100%)
        const maxScore = results[0].score;
        if (maxScore > 1.0) {
            results.forEach(r => {
                r.score = r.score / maxScore;
            });
        }

        const top1 = results[0];
        const topConfidencePercent = (top1.score * 100).toFixed(1);
        const friendlyName = labelFriendlyNames[top1.label] || top1.label;

        // Render Top 1
        document.getElementById('top-label').textContent = top1.label;
        document.getElementById('top-friendly-name').textContent = friendlyName;
        document.getElementById('top-confidence').textContent = `${topConfidencePercent}%`;
        
        // Render Runner Ups (Top 2 and 3)
        const alternativesContainer = document.getElementById('alternative-list');
        alternativesContainer.innerHTML = '';
        
        for (let i = 1; i <= 2; i++) {
            const item = results[i];
            const percent = (item.score * 100).toFixed(1);
            const name = labelFriendlyNames[item.label] || item.label;
            
            const altHtml = `
                <div class="p-3 rounded-xl bg-slate-800/40 border border-slate-800 hover:bg-slate-800/60 transition-all">
                    <div class="flex items-center justify-between mb-1">
                        <div>
                            <span class="text-xs font-bold text-white">${item.label}</span>
                            <span class="text-[10px] text-slate-400 ml-2">${name}</span>
                        </div>
                        <span class="text-xs font-black text-slate-400">${percent}%</span>
                    </div>
                    <div class="w-full h-1.5 bg-slate-900 rounded-full overflow-hidden">
                        <div class="h-full bg-slate-600 rounded-full" style="width: ${percent}%"></div>
                    </div>
                </div>
            `;
            alternativesContainer.insertAdjacentHTML('beforeend', altHtml);
        }

        // Hide loading state and show results
        document.getElementById('ai-loading').classList.add('hidden');
        document.getElementById('ai-success').classList.remove('hidden');

        // Animate top progress bar after displaying
        setTimeout(() => {
            document.getElementById('top-progress-bar').style.width = `${topConfidencePercent}%`;
        }, 100);

        // 6. Save prediction to DB via Fetch API
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        fetch("{{ route('cv.saveCategory') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                category: top1.label,
                confidence: parseFloat(topConfidencePercent)
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                console.log('✅ AI Career Category saved to profile.');
            }
        })
        .catch(err => console.error('❌ Failed to save AI prediction:', err));

        // 7. Highlight Job Cards matching AI predicted Category
        const dbCategorySlug = labelToDbCategory[top1.label];
        if (dbCategorySlug) {
            const jobCards = document.querySelectorAll('.job-rec-card');
            let matchedCount = 0;
            
            jobCards.forEach(card => {
                const cardSlug = card.getAttribute('data-category-slug');
                if (cardSlug === dbCategorySlug) {
                    matchedCount++;
                    // Add AI badge
                    const placeholder = card.querySelector('.ai-badge-placeholder');
                    if (placeholder) {
                        placeholder.innerHTML = `
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-purple-500/20 text-purple-400 text-[9px] font-black uppercase tracking-wider border border-purple-500/30 shadow-lg shadow-purple-500/10 animate-pulse">
                                <i class="fas fa-brain"></i> AI Match
                            </span>
                        `;
                    }
                    // Highlight card border and shadow
                    card.classList.remove('border-slate-700', 'border-indigo-500/50');
                    card.classList.add('border-purple-500/80', 'shadow-2xl', 'shadow-purple-500/5');
                }
            });
            
            console.log(`🤖 Highlighted ${matchedCount} jobs for category: ${dbCategorySlug}`);
        }

    } catch (error) {
        console.error('❌ TensorFlow.js Inference Error:', error);
        document.getElementById('ai-loading').classList.add('hidden');
        const errEl = document.getElementById('ai-error');
        
        let tfInfo = 'undefined';
        if (typeof tf !== 'undefined') {
            tfInfo = `Type: ${typeof tf}, Keys: ${Object.keys(tf).slice(0, 15).join(', ')}`;
        }
        
        errEl.innerHTML = `
            <i class="fas fa-exclamation-triangle text-3xl text-red-500 mb-2"></i>
            <p class="text-sm font-medium">Gagal menjalankan model AI. Menggunakan fallback analisis kata kunci.</p>
            <div class="mt-3 p-3 bg-red-500/10 border border-red-500/20 rounded-lg text-left max-w-lg mx-auto overflow-auto">
                <p class="font-mono text-[11px] text-red-400 font-bold mb-1">Error Details:</p>
                <pre class="font-mono text-[10px] text-red-300 whitespace-pre-wrap">${error.stack || error.message || error}</pre>
                <p class="font-mono text-[11px] text-yellow-400 font-bold mt-2 mb-1">tf Object Info:</p>
                <pre class="font-mono text-[10px] text-yellow-300">${tfInfo}</pre>
            </div>
        `;
        errEl.classList.remove('hidden');
    }
}

if (document.readyState === 'complete' || document.readyState === 'interactive') {
    startAIPrediction();
} else {
    document.addEventListener('DOMContentLoaded', startAIPrediction);
}
</script>
@endpush
@endsection
