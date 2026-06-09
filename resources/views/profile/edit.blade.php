@extends('layouts.app')

@section('title', 'Pengaturan Profil')

@section('content')
<div class="w-full space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-center gap-4" data-aos="fade-down">
        <!-- Profile Photo Mini -->
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-full overflow-hidden border-2 border-slate-700 bg-slate-800 shrink-0 shadow-lg">
                @if($user->profile?->avatar)
                    <img src="{{ asset('storage/' . $user->profile->avatar) }}" class="w-full h-full object-cover" alt="{{ $user->name }}">
                @else
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=0D8ABC&color=fff" class="w-full h-full object-cover" alt="{{ $user->name }}">
                @endif
            </div>

            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white">Pengaturan Profil</h1>
                <p class="text-slate-400 text-sm mt-1">Kelola informasi profesional dan keamanan akun Anda.</p>
            </div>
        </div>
    </div>

    <div class="space-y-8">
        <!-- Profile Info -->
        <div class="glass p-8" data-aos="fade-up">
            <div class="w-full">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <!-- Skills & Interests Section -->
        <div class="glass p-8 animate-fade-in">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
                <h3 class="text-lg sm:text-xl font-bold text-white"><i class="fas fa-microchip text-blue-400 mr-2"></i> Keahlian Terdeteksi</h3>
                <div class="flex gap-2">
                    @if(Auth::user()->skills->count() > 0)
                    <form action="{{ route('cv.reset') }}" method="POST" class="inline" id="cvResetForm">
                        @csrf
                        <button type="button" onclick="confirmCvReset()" class="px-3 py-1.5 rounded-lg bg-red-500/10 text-red-400 text-xs font-bold hover:bg-red-500/20 transition-all">
                            <i class="fas fa-trash-alt mr-1"></i> Reset
                        </button>
                    </form>
                    @endif
                    <a href="{{ route('cv.index') }}" class="px-3 py-1.5 rounded-lg bg-blue-600/10 text-blue-400 text-xs font-bold hover:bg-blue-600/20 transition-all">
                        <i class="fas fa-upload mr-1"></i> {{ Auth::user()->skills->count() > 0 ? 'Unggah Ulang CV' : 'Unggah CV' }}
                    </a>
                </div>
            </div>

            @if(Auth::user()->skills->count() > 0)
            <div class="flex flex-wrap gap-3 mb-6">
                @foreach(Auth::user()->skills as $skill)
                <div class="px-4 py-2 rounded-xl {{ $skill->pivot->source === 'cv' ? 'bg-blue-600/10 border-blue-500/20' : 'bg-emerald-600/10 border-emerald-500/20' }} border text-sm font-bold flex items-center gap-2">
                    <i class="fas fa-check-circle {{ $skill->pivot->source === 'cv' ? 'text-blue-400' : 'text-emerald-400' }}"></i>
                    <span class="text-white">{{ $skill->name }}</span>
                    <span class="text-[9px] {{ $skill->pivot->source === 'cv' ? 'bg-blue-500' : 'bg-emerald-500' }} text-white px-1.5 py-0.5 rounded-full ml-1">Lvl {{ $skill->pivot->level }}</span>
                    <span class="text-[8px] {{ $skill->pivot->source === 'cv' ? 'text-blue-500' : 'text-emerald-500' }} uppercase font-bold tracking-wider">{{ $skill->pivot->source }}</span>
                </div>
                @endforeach
            </div>

            @if(Auth::user()->interests->count() > 0)
            <div class="pt-5 border-t border-slate-800">
                <h4 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-3"><i class="fas fa-heart text-purple-400 mr-1"></i> Minat Terdeteksi</h4>
                <div class="flex flex-wrap gap-2">
                    @foreach(Auth::user()->interests as $interest)
                    <span class="px-3 py-1.5 rounded-full bg-purple-500/10 text-purple-400 text-xs font-bold">{{ $interest->name }}</span>
                    @endforeach
                </div>
            </div>
            @endif

            @if($user->profile?->cv_career_category)
            <div class="pt-5 border-t border-slate-800">
                <h4 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-3">
                    <i class="fas fa-brain text-purple-400 mr-1"></i> Klasifikasi Karir Deep AI
                </h4>
                <div class="flex flex-col sm:flex-row sm:items-center gap-4 bg-purple-500/5 border border-purple-500/10 rounded-xl p-4">
                    <div>
                        <span class="text-xs text-slate-400">Prediksi Kategori Utama:</span>
                        <div class="flex flex-col sm:flex-row sm:items-baseline gap-1 sm:gap-2 mt-1">
                            <span class="text-base font-extrabold text-white">{{ $user->profile->cv_career_category }}</span>
                            @php
                                $labelFriendlyNames = [
                                    "ACCOUNTANT" => "Akuntan / Keuangan",
                                    "ADVOCATE" => "Advokat / Hukum",
                                    "AGRICULTURE" => "Pertanian & Agronomi",
                                    "APPAREL" => "Mode & Pakaian",
                                    "ARTS" => "Seni & Industri Kreatif",
                                    "AUTOMOBILE" => "Teknik Otomotif",
                                    "AVIATION" => "Penerbangan / Dirgantara",
                                    "BANKING" => "Perbankan / Layanan Finansial",
                                    "BPO" => "BPO & Customer Service",
                                    "BUSINESS-DEVELOPMENT" => "Pengembangan Bisnis",
                                    "CHEF" => "Kulinari & Tata Boga",
                                    "CONSTRUCTION" => "Konstruksi / Sipil",
                                    "CONSULTANT" => "Konsultan Bisnis",
                                    "DESIGNER" => "Desain Grafis / UI/UX",
                                    "DIGITAL-MEDIA" => "Media Digital & Periklanan",
                                    "ENGINEERING" => "Rekayasa & Teknik Umum",
                                    "FINANCE" => "Keuangan & Analis Finansial",
                                    "FITNESS" => "Kebugaran & Kesehatan",
                                    "HEALTHCARE" => "Layanan Kesehatan & Medis",
                                    "HR" => "Sumber Daya Manusia (HRD)",
                                    "INFORMATION-TECHNOLOGY" => "Teknologi Informasi & Software",
                                    "PUBLIC-RELATIONS" => "Hubungan Masyarakat (PR)",
                                    "SALES" => "Penjualan & Pemasaran",
                                    "TEACHER" => "Pendidik & Guru"
                                ];
                                $friendly = $labelFriendlyNames[$user->profile->cv_career_category] ?? $user->profile->cv_career_category;
                            @endphp
                            <span class="text-xs text-purple-400 font-medium">({{ $friendly }})</span>
                        </div>
                    </div>
                    <div class="sm:ml-auto w-full sm:w-48 shrink-0">
                        <div class="flex justify-between items-baseline mb-1">
                            <span class="text-[9px] text-slate-500 uppercase font-bold">Confidence</span>
                            <span class="text-xs font-extrabold text-purple-400">{{ $user->profile->cv_career_confidence }}%</span>
                        </div>
                        <div class="w-full h-1.5 bg-slate-800 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-purple-500 to-blue-500" style="width: {{ $user->profile->cv_career_confidence }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            @else
            <div class="text-center py-8 border-2 border-dashed border-slate-800 rounded-2xl">
                <div class="w-16 h-16 mx-auto bg-slate-800 rounded-2xl flex items-center justify-center mb-4">
                    <i class="fas fa-file-pdf text-slate-600 text-2xl"></i>
                </div>
                <p class="text-slate-500 text-sm mb-4">Belum ada keahlian terdeteksi. Unggah CV Anda untuk mengidentifikasi keahlian secara otomatis!</p>
                <a href="{{ route('cv.index') }}" class="btn-premium px-6 py-2 text-sm">
                    <i class="fas fa-wand-magic-sparkles mr-2"></i> Analisis CV Saya
                </a>
            </div>
            @endif
        </div>

        <!-- Password Update -->
        <div class="glass p-8" data-aos="fade-up" data-aos-delay="100">
            <div class="w-full">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <!-- Danger Zone -->
        <div class="glass p-8 border-l-4 border-rose-500" data-aos="fade-up" data-aos-delay="200">
            <div class="w-full">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</div>

<x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
    <form method="post" action="{{ route('profile.destroy') }}" class="p-8 space-y-6">
        @csrf
        @method('delete')

        <h2 class="text-xl font-bold text-white">
            Apakah Anda yakin ingin menghapus akun Anda?
        </h2>

        <p class="mt-2 text-sm text-slate-400">
            Setelah akun Anda dihapus, semua sumber daya dan data akan dihapus secara permanen. Masukkan kata sandi Anda untuk mengonfirmasi bahwa Anda ingin menghapus akun secara permanen.
        </p>

        <div class="mt-6">
            <label for="password" class="sr-only">Kata Sandi</label>
            <x-text-input
                id="password"
                name="password"
                type="password"
                class="mt-1 block w-full bg-slate-900/50 border-slate-700 text-white py-4 px-6 text-lg rounded-2xl"
                placeholder="Kata Sandi"
            />
            <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
        </div>

        <div class="mt-6 flex justify-end gap-4">
            <button type="button" x-on:click="$dispatch('close')" class="px-6 py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-semibold rounded-xl transition-all duration-300 transform active:scale-95">
                Batal
            </button>

            <button type="submit" class="btn-danger-premium px-6 py-2.5">
                Hapus Akun
            </button>
        </div>
    </form>
</x-modal>
@endsection

@push('scripts')
<script>
function confirmCvReset() {
    Swal.fire({
        title: 'Reset Data CV?',
        text: 'Ini akan menghapus semua keahlian dan minat yang terdeteksi dari CV Anda. Anda bisa unggah ulang kapan saja.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#334155',
        confirmButtonText: 'Ya, Reset',
        cancelButtonText: 'Batal',
        background: '#1e293b',
        color: '#fff'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('cvResetForm').submit();
        }
    });
}
</script>
@endpush
