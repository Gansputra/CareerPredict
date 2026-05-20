<section class="space-y-6">
    <header>
        <h2 class="text-xl font-bold text-white">
            Hapus Akun
        </h2>
        <p class="mt-1 text-sm text-slate-400">
            Setelah akun Anda dihapus, semua sumber daya dan data akan dihapus secara permanen. Sebelum menghapus akun, silakan unduh data atau informasi yang ingin Anda simpan.
        </p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="px-8 py-3 rounded-xl"
    >Hapus Akun</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-8 bg-slate-900 border border-slate-800">
            @csrf
            @method('delete')

            <h2 class="text-xl font-bold text-white">
                Apakah Anda yakin ingin menghapus akun Anda?
            </h2>

            <p class="mt-1 text-sm text-slate-400">
                Setelah akun Anda dihapus, semua sumber daya dan data akan dihapus secara permanen. Masukkan kata sandi Anda untuk mengonfirmasi bahwa Anda ingin menghapus akun secara permanen.
            </p>

            <div class="mt-6">
                <label for="password" class="sr-only">Kata Sandi</label>
                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-full bg-slate-800 border-slate-700 text-white"
                    placeholder="Kata Sandi"
                />
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end gap-4">
                <x-secondary-button x-on:click="$dispatch('close')" class="px-6 py-2">
                    Batal
                </x-secondary-button>

                <x-danger-button class="px-6 py-2">
                    Hapus Akun
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
