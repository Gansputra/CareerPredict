<section class="space-y-6">
    <header>
        <h2 class="text-xl font-bold text-white">
            {{ __('Delete Account') }}
        </h2>
        <p class="mt-1 text-sm text-slate-400">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="px-8 py-3 rounded-xl"
    >{{ __('Delete Account') }}</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-8 bg-slate-900 border border-slate-800">
            @csrf
            @method('delete')

            <h2 class="text-xl font-bold text-white">
                {{ __('Are you sure you want to delete your account?') }}
            </h2>

            <p class="mt-1 text-sm text-slate-400">
                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
            </p>

            <div class="mt-6">
                <label for="password" class="sr-only">Password</label>
                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-full bg-slate-800 border-slate-700 text-white"
                    placeholder="{{ __('Password') }}"
                />
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end gap-4">
                <x-secondary-button x-on:click="$dispatch('close')" class="px-6 py-2">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-danger-button class="px-6 py-2">
                    {{ __('Delete Account') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
