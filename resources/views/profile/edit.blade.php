@extends('layouts.app')

@section('title', 'My Profile Settings')

@section('content')
<div class="max-w-7xl mx-auto space-y-8">
    <div class="flex items-center justify-between" data-aos="fade-down">
        <div>
            <h1 class="text-3xl font-extrabold text-white">Profile Settings</h1>
            <p class="text-slate-400 mt-1">Manage your professional information and account security.</p>
        </div>
    </div>

    <div class="space-y-8">
        <!-- Profile Info -->
        <div class="glass p-8" data-aos="fade-up">
            <div class="max-w-4xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <!-- Password Update -->
        <div class="glass p-8" data-aos="fade-up" data-aos-delay="100">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <!-- Danger Zone -->
        <div class="glass p-8 border-l-4 border-rose-500" data-aos="fade-up" data-aos-delay="200">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</div>
@endsection
