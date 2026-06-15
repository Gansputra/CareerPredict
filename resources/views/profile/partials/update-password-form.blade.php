<section>
    <header>
        <h2 class="text-2xl font-bold text-white">
            Ubah Kata Sandi
        </h2>
        <p class="mt-1 text-sm text-slate-400">
            Pastikan akun Anda menggunakan kata sandi yang panjang dan acak agar tetap aman.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-8 space-y-8">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Kata Sandi Saat Ini</label>
            <x-text-input id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full bg-slate-900/50 border-slate-700 text-white py-2.5 px-4 text-sm rounded-xl" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <label for="update_password_password" class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Kata Sandi Baru</label>
            <x-text-input id="update_password_password" name="password" type="password" class="mt-1 block w-full bg-slate-900/50 border-slate-700 text-white py-2.5 px-4 text-sm rounded-xl" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <label for="update_password_password_confirmation" class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Konfirmasi Kata Sandi</label>
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full bg-slate-900/50 border-slate-700 text-white py-2.5 px-4 text-sm rounded-xl" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4 pt-4">
            <button type="submit" class="btn-premium px-10 py-3">Ubah Kata Sandi</button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 5000)"
                    class="text-sm text-emerald-500 font-medium"
                >Kata sandi diperbarui.</p>
            @endif
        </div>
    </form>
</section>
