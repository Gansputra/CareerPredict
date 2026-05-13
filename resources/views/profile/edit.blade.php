@extends('layouts.app')

@section('title', 'My Profile Settings')

@section('content')
<div class="w-full space-y-8">
    <div class="flex items-center justify-between" data-aos="fade-down">
        <div>
            <h1 class="text-3xl font-extrabold text-white">Profile Settings</h1>
            <p class="text-slate-400 mt-1">Manage your professional information and account security.</p>
        </div>
    </div>

    <div class="space-y-8">
        <!-- Profile Info -->
        <div class="glass p-8" data-aos="fade-up">
            <div class="w-full">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <!-- Skills Section -->
        <div class="glass p-8" data-aos="fade-up">
            <h3 class="text-xl font-bold text-white mb-6">Your Detected Skills</h3>
            <div class="flex flex-wrap gap-3">
                @forelse(Auth::user()->skills as $skill)
                <div class="px-4 py-2 rounded-xl bg-blue-600/10 border border-blue-500/20 text-blue-500 text-sm font-bold flex items-center gap-2">
                    <i class="fas fa-check-circle"></i>
                    {{ $skill->name }}
                    <span class="text-[10px] bg-blue-500 text-white px-1.5 rounded ml-1">Lvl {{ $skill->pivot->level }}</span>
                </div>
                @empty
                <div class="text-slate-500 text-sm italic py-4 border-2 border-dashed border-slate-800 rounded-2xl w-full text-center">
                    No skills detected yet. Try uploading your CV (PDF) to automatically identify your expertise!
                </div>
                @endforelse
            </div>
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
