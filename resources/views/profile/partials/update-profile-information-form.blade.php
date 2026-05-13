<section>
    <header>
        <h2 class="text-2xl font-bold text-white">
            {{ __('Professional Profile') }}
        </h2>
        <p class="mt-1 text-sm text-slate-400">
            {{ __("Complete your professional identity to improve AI recommendations.") }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="mt-8 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Basic Info -->
            <div class="space-y-6">
                <div>
                    <label for="name" class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Full Name</label>
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full bg-slate-900/50 border-slate-700 text-white" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                </div>

                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Email Address</label>
                    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full bg-slate-900/50 border-slate-700 text-white" :value="old('email', $user->email)" required autocomplete="username" />
                    <x-input-error class="mt-2" :messages="$errors->get('email')" />
                </div>

                <div>
                    <label for="phone" class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Phone Number</label>
                    <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full bg-slate-900/50 border-slate-700 text-white" :value="old('phone', $user->profile?->phone)" placeholder="+62..." />
                    <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                </div>
            </div>

            <!-- Professional Info -->
            <div class="space-y-6">
                <div>
                    <label for="headline" class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Professional Headline</label>
                    <x-text-input id="headline" name="headline" type="text" class="mt-1 block w-full bg-slate-900/50 border-slate-700 text-white" :value="old('headline', $user->profile?->headline)" placeholder="e.g. Senior Software Engineer" />
                    <x-input-error class="mt-2" :messages="$errors->get('headline')" />
                </div>

                <div>
                    <label for="bio" class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Brief Bio</label>
                    <textarea id="bio" name="bio" rows="4" class="mt-1 block w-full bg-slate-900/50 border-slate-700 text-white rounded-xl focus:ring-blue-500 focus:border-blue-500">{{ old('bio', $user->profile?->bio) }}</textarea>
                    <x-input-error class="mt-2" :messages="$errors->get('bio')" />
                </div>
            </div>
        </div>

        <!-- CV Upload -->
        <div class="pt-6 border-t border-slate-800">
            <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-4">Curriculum Vitae (CV)</label>
            <div class="flex items-center gap-6 p-6 bg-slate-900/30 border border-slate-800 rounded-2xl">
                <div class="w-16 h-16 bg-blue-600/10 rounded-xl flex items-center justify-center text-blue-500 text-2xl">
                    <i class="fas fa-file-pdf"></i>
                </div>
                <div class="flex-1">
                    <input type="file" name="cv" id="cv" class="block w-full text-sm text-slate-400
                        file:mr-4 file:py-2 file:px-4
                        file:rounded-full file:border-0
                        file:text-sm file:font-semibold
                        file:bg-blue-600/10 file:text-blue-500
                        hover:file:bg-blue-600/20 transition-all cursor-pointer">
                    <p class="mt-2 text-[10px] text-slate-500">PDF, DOC, or DOCX up to 5MB</p>
                </div>
                @if($user->profile?->cv_path)
                <a href="{{ Storage::url($user->profile->cv_path) }}" target="_blank" class="px-4 py-2 bg-slate-800 text-white text-xs font-bold rounded-lg hover:bg-slate-700 transition-all">
                    View Current CV
                </a>
                @endif
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('cv')" />
        </div>

        <div class="flex items-center gap-4 pt-6">
            <button type="submit" class="btn-premium px-10 py-3">{{ __('Save Profile') }}</button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-emerald-500 font-medium"
                >{{ __('Changes saved successfully.') }}</p>
            @endif
        </div>
    </form>
</section>
