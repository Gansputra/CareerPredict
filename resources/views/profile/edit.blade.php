@extends('layouts.app')

@section('title', 'My Profile Settings')

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
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white">Profile Settings</h1>
                <p class="text-slate-400 text-sm mt-1">Manage your professional information and account security.</p>
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
                <h3 class="text-lg sm:text-xl font-bold text-white"><i class="fas fa-microchip text-blue-400 mr-2"></i> Your Detected Skills</h3>
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
                        <i class="fas fa-upload mr-1"></i> {{ Auth::user()->skills->count() > 0 ? 'Re-upload CV' : 'Upload CV' }}
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
                <h4 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-3"><i class="fas fa-heart text-purple-400 mr-1"></i> Detected Interests</h4>
                <div class="flex flex-wrap gap-2">
                    @foreach(Auth::user()->interests as $interest)
                    <span class="px-3 py-1.5 rounded-full bg-purple-500/10 text-purple-400 text-xs font-bold">{{ $interest->name }}</span>
                    @endforeach
                </div>
            </div>
            @endif
            @else
            <div class="text-center py-8 border-2 border-dashed border-slate-800 rounded-2xl">
                <div class="w-16 h-16 mx-auto bg-slate-800 rounded-2xl flex items-center justify-center mb-4">
                    <i class="fas fa-file-pdf text-slate-600 text-2xl"></i>
                </div>
                <p class="text-slate-500 text-sm mb-4">No skills detected yet. Upload your CV to automatically identify your expertise!</p>
                <a href="{{ route('cv.index') }}" class="btn-premium px-6 py-2 text-sm">
                    <i class="fas fa-wand-magic-sparkles mr-2"></i> Analyze My CV
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
@endsection

@push('scripts')
<script>
function confirmCvReset() {
    Swal.fire({
        title: 'Reset CV Data?',
        text: 'This will remove all skills and interests detected from your CV. You can re-upload anytime.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#334155',
        confirmButtonText: 'Yes, Reset',
        cancelButtonText: 'Cancel',
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
