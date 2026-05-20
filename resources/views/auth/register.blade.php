<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" class="space-y-6">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Nama Lengkap</label>
            <div class="relative">
                <i class="fas fa-user absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-sm"></i>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                    class="w-full bg-slate-900/50 border-slate-700 rounded-xl pl-12 pr-4 py-3 text-white focus:ring-blue-500 focus:border-blue-500 transition-all placeholder-slate-600"
                    placeholder="Nama Lengkap Anda">
            </div>
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Alamat Email</label>
            <div class="relative">
                <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-sm"></i>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                    class="w-full bg-slate-900/50 border-slate-700 rounded-xl pl-12 pr-4 py-3 text-white focus:ring-blue-500 focus:border-blue-500 transition-all placeholder-slate-600"
                    placeholder="name@company.com">
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Kata Sandi</label>
            <div class="relative">
                <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-sm"></i>
                <input id="password" type="password" name="password" required autocomplete="new-password"
                    class="w-full bg-slate-900/50 border-slate-700 rounded-xl pl-12 pr-4 py-3 text-white focus:ring-blue-500 focus:border-blue-500 transition-all placeholder-slate-600"
                    placeholder="••••••••">
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Konfirmasi Kata Sandi</label>
            <div class="relative">
                <i class="fas fa-check-double absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-sm"></i>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                    class="w-full bg-slate-900/50 border-slate-700 rounded-xl pl-12 pr-4 py-3 text-white focus:ring-blue-500 focus:border-blue-500 transition-all placeholder-slate-600"
                    placeholder="••••••••">
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div>
            <button type="submit" class="w-full btn-premium py-4">
                Buat Akun Gratis
            </button>
        </div>

        <div class="text-center mt-6">
            <p class="text-xs text-slate-500">Sudah terdaftar? 
                <a href="{{ route('login') }}" class="text-blue-500 font-bold hover:underline ml-1">Masuk di sini</a>
            </p>
        </div>
    </form>
</x-guest-layout>
