<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Alamat Email</label>
            <div class="relative">
                <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-sm"></i>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                    class="w-full bg-slate-900/50 border-slate-700 rounded-xl pl-12 pr-4 py-3 text-white focus:ring-blue-500 focus:border-blue-500 transition-all placeholder-slate-600"
                    placeholder="name@company.com">
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between mb-2">
                <label for="password" class="block text-xs font-bold uppercase tracking-widest text-slate-500">Kata Sandi</label>
                @if (Route::has('password.request'))
                    <a class="text-[10px] font-bold text-blue-500 hover:text-blue-400 uppercase tracking-widest" href="{{ route('password.request') }}">
                        Lupa?
                    </a>
                @endif
            </div>
            <div class="relative">
                <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-sm"></i>
                <input id="password" type="password" name="password" required autocomplete="current-password"
                    class="w-full bg-slate-900/50 border-slate-700 rounded-xl pl-12 pr-4 py-3 text-white focus:ring-blue-500 focus:border-blue-500 transition-all placeholder-slate-600"
                    placeholder="••••••••">
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                <input id="remember_me" type="checkbox" class="rounded-lg bg-slate-900 border-slate-700 text-blue-600 shadow-sm focus:ring-blue-500 focus:ring-offset-slate-900" name="remember">
                <span class="ms-2 text-xs text-slate-500 group-hover:text-slate-300 transition-colors">Ingat saya</span>
            </label>
        </div>

        <div>
            <button type="submit" class="w-full btn-premium py-4">
                Masuk ke CareerPredict
            </button>
        </div>

        <div class="text-center mt-6">
            <p class="text-xs text-slate-500">Belum punya akun? 
                <a href="{{ route('register') }}" class="text-blue-500 font-bold hover:underline ml-1">Buat Akun</a>
            </p>
        </div>
    </form>
</x-guest-layout>
