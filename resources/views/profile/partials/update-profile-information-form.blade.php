<section>
    <header>
        <h2 class="text-2xl font-bold text-white">
            {{ __('Professional Profile') }}
        </h2>
        <p class="mt-1 text-sm text-slate-400">
            {{ __("Complete your professional identity to improve AI recommendations.") }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="mt-8 space-y-10" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <!-- One Column Full Width Layout -->
        <div class="space-y-8">
            <div class="grid grid-cols-1 gap-8">
                
                <!-- PHOTO UPLOAD SECTION -->
                <div x-data="{ photoName: null, photoPreview: null }" class="flex items-center gap-6">
                    <!-- Preview Container -->
                    <div class="relative w-24 h-24 rounded-full overflow-hidden bg-slate-800 border-2 border-slate-700 shrink-0">
                        <!-- Current Profile Photo -->
                        <div x-show="!photoPreview" class="w-full h-full">
                            @if($user->profile?->avatar)
                                <img src="{{ asset('storage/' . $user->profile->avatar) }}" class="w-full h-full object-cover">
                            @else
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=0D8ABC&color=fff" class="w-full h-full object-cover">
                            @endif
                        </div>

                        <!-- New Profile Photo Preview -->
                        <div x-show="photoPreview" class="w-full h-full" style="display: none;">
                            <span class="block w-full h-full bg-cover bg-no-repeat bg-center ignore-invert" x-bind:style="'background-image: url(\'' + photoPreview + '\');'"></span>
                        </div>
                    </div>

                    <!-- Upload Button & Info -->
                    <div>
                        <input type="file" id="photo" name="photo" class="hidden" x-ref="photo"
                            x-on:change="
                                photoName = $refs.photo.files[0].name;
                                const reader = new FileReader();
                                reader.onload = (e) => {
                                    photoPreview = e.target.result;
                                };
                                reader.readAsDataURL($refs.photo.files[0]);
                            ">
                        
                        <label for="photo" class="cursor-pointer inline-flex items-center px-4 py-2 bg-slate-800 border border-slate-600 rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-slate-700 transition ease-in-out duration-150">
                            <i class="fas fa-camera mr-2"></i> Select New Photo
                        </label>
                        
                        <div class="mt-2 text-xs text-slate-500">
                            JPG, JPEG or PNG. Max size of 2MB.
                        </div>
                        <x-input-error class="mt-2" :messages="$errors->get('photo')" />
                    </div>
                </div>

                <div>
                    <label for="name" class="block text-sm font-bold uppercase tracking-widest text-slate-500 mb-2">Full Name</label>
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full bg-slate-900/50 border-slate-700 text-white py-4 px-6 text-lg" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                </div>

                <div>
                    <label for="email" class="block text-sm font-bold uppercase tracking-widest text-slate-500 mb-2">Email Address</label>
                    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full bg-slate-900/50 border-slate-700 text-white py-4 px-6 text-lg" :value="old('email', $user->email)" required autocomplete="username" />
                    <x-input-error class="mt-2" :messages="$errors->get('email')" />
                </div>

                <div>
                    <label for="phone" class="block text-sm font-bold uppercase tracking-widest text-slate-500 mb-2">Phone Number</label>
                    <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full bg-slate-900/50 border-slate-700 text-white py-4 px-6 text-lg" :value="old('phone', $user->profile?->phone)" placeholder="+62..." />
                    <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                </div>

                <div>
                    <label for="headline" class="block text-sm font-bold uppercase tracking-widest text-slate-500 mb-2">Professional Headline</label>
                    <x-text-input id="headline" name="headline" type="text" class="mt-1 block w-full bg-slate-900/50 border-slate-700 text-white py-4 px-6 text-lg" :value="old('headline', $user->profile?->headline)" placeholder="e.g. Senior Software Engineer" />
                    <x-input-error class="mt-2" :messages="$errors->get('headline')" />
                </div>

                <div>
                    <label for="bio" class="block text-sm font-bold uppercase tracking-widest text-slate-500 mb-2">Brief Bio</label>
                    <textarea id="bio" name="bio" rows="6" class="mt-1 block w-full bg-slate-900/50 border-slate-700 text-white rounded-2xl focus:ring-blue-500 focus:border-blue-500 p-6 text-lg">{{ old('bio', $user->profile?->bio) }}</textarea>
                    <x-input-error class="mt-2" :messages="$errors->get('bio')" />
                </div>
            </div>
        </div>


        <div class="flex items-center gap-4 pt-6">
            <button type="submit" class="btn-premium px-10 py-3">{{ __('Save Profile') }}</button>

            @if (session('success'))
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 5000)"
                    class="text-sm text-emerald-500 font-medium"
                >{{ session('success') }}</p>
            @endif
        </div>
    </form>
</section>
