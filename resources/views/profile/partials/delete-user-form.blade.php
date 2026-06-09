<section class="space-y-6">
    <header>
        <h2 class="text-2xl font-bold text-white">
            Hapus Akun
        </h2>
        <p class="mt-1 text-sm text-slate-400">
            Setelah akun Anda dihapus, semua sumber daya dan data akan dihapus secara permanen. Sebelum menghapus akun, silakan unduh data atau informasi yang ingin Anda simpan.
        </p>
    </header>

    <button
        type="button"
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="btn-danger-premium px-8 py-3"
    >Hapus Akun</button>
</section>
