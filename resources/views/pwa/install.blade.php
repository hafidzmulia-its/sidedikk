<x-guest-layout>
    <main class="flex min-h-[calc(100vh-2rem)] flex-col px-5 pb-8 pt-6">
        <div class="mx-auto w-full max-w-[420px]">
            <div class="mb-6 flex items-center justify-between">
                <a href="{{ route('home') }}" class="sid-top-icon" aria-label="Kembali">
                    <span class="material-symbols-outlined">arrow_back</span>
                </a>
                <p class="text-sm font-semibold text-[#95409e]">Pemasangan Aplikasi</p>
                <div class="h-10 w-10"></div>
            </div>

            <section class="sid-card overflow-hidden border border-[#efddef] px-5 pb-6 pt-6">
                <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-[#fff2fa] shadow-[0_8px_20px_rgba(149,64,158,0.10)]">
                    <img src="{{ asset('icon.png') }}" alt="SIDEDIKK" class="h-16 w-16 rounded-full object-contain">
                </div>

                <div class="mt-5 text-center">
                    <h1 class="text-[24px] font-bold tracking-[-0.02em] text-[#221825]">Pasang SIDEDIKK</h1>
                    <p class="mt-2 text-sm leading-6 text-[#50434e]">
                        Tambahkan SIDEDIKK ke layar utama agar lebih cepat dibuka dan terasa seperti aplikasi.
                    </p>
                </div>

                <div class="mt-5 grid gap-3">
                    <button
                        type="button"
                        data-pwa-install-prompt
                        class="sid-button-primary hidden h-12 w-full"
                    >
                        Pasang Sekarang
                    </button>

                    <a
                        href="{{ auth()->check() ? route('dashboard') : route('login') }}"
                        class="sid-button-secondary h-12 w-full border-[#d7c3dd] text-[#95409e]"
                    >
                        {{ auth()->check() ? 'Buka Aplikasi' : 'Masuk' }}
                    </a>
                </div>

                <div class="mt-4 space-y-3">
                    <div data-pwa-install-state="ready" class="hidden rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-[#238a57]">
                        Browser ini mendukung pemasangan aplikasi. Tekan <strong>Pasang Sekarang</strong> untuk melanjutkan.
                    </div>

                    <div data-pwa-install-state="installed" class="hidden rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-[#238a57]">
                        SIDEDIKK sudah terpasang di perangkat ini. Buka aplikasi dari layar utama atau tekan tombol di bawah.
                    </div>

                    <div data-pwa-install-state="ios" class="hidden rounded-2xl border border-[#efddef] bg-[#fff8fc] px-4 py-3 text-sm text-[#50434e]">
                        Safari di iPhone/iPad tidak menyediakan tombol install otomatis. Ikuti panduan di bawah untuk menambahkan aplikasi ke layar utama.
                    </div>

                    <div data-pwa-install-state="unsupported" class="hidden rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-[#8a5b00]">
                        Browser ini belum memberikan prompt pemasangan otomatis. Anda masih bisa memakai SIDEDIKK lewat browser atau mengikuti panduan manual bila tersedia.
                    </div>
                </div>
            </section>

            <section class="mt-5 grid gap-4">
                <article class="sid-card border border-[#efddef] p-5">
                    <h2 class="text-base font-semibold text-[#221825]">Panduan Android</h2>
                    <ol class="mt-3 space-y-2 text-sm leading-6 text-[#50434e]">
                        <li>1. Buka halaman ini di Chrome atau browser Android yang mendukung PWA.</li>
                        <li>2. Tunggu tombol <strong>Pasang Sekarang</strong> aktif atau muncul pop-up pemasangan.</li>
                        <li>3. Konfirmasi pemasangan, lalu buka SIDEDIKK dari layar utama.</li>
                    </ol>
                    <p data-pwa-install-note="ready" class="mt-3 hidden text-xs text-[#238a57]">
                        Prompt pemasangan sudah tersedia di browser ini.
                    </p>
                    <p data-pwa-install-note="unsupported" class="mt-3 hidden text-xs text-[#8a5b00]">
                        Jika prompt tidak muncul, buka menu browser lalu pilih <strong>Install app</strong> atau <strong>Tambahkan ke layar utama</strong>.
                    </p>
                </article>

                <article class="sid-card border border-[#efddef] p-5">
                    <h2 class="text-base font-semibold text-[#221825]">Panduan iPhone &amp; iPad</h2>
                    <ol class="mt-3 space-y-2 text-sm leading-6 text-[#50434e]">
                        <li>1. Tekan tombol <strong>Bagikan</strong>.</li>
                        <li>2. Pilih <strong>Tambahkan ke Layar Utama</strong>.</li>
                        <li>3. Konfirmasi pemasangan, lalu buka SIDEDIKK dari ikon yang tersimpan.</li>
                    </ol>
                    <p data-pwa-install-note="ios" class="mt-3 hidden text-xs text-[#95409e]">
                        Langkah ini diperlukan karena Safari tidak mendukung prompt install otomatis.
                    </p>
                </article>

                <article class="sid-card border border-[#efddef] p-5">
                    <h2 class="text-base font-semibold text-[#221825]">Jika Sudah Terpasang</h2>
                    <p class="mt-3 text-sm leading-6 text-[#50434e]">
                        Setelah aplikasi terpasang, buka SIDEDIKK dari layar utama agar tampil dalam mode aplikasi penuh.
                    </p>
                    <p data-pwa-install-note="installed" class="mt-3 hidden text-xs text-[#238a57]">
                        Sistem mendeteksi mode aplikasi terpasang pada perangkat ini.
                    </p>
                </article>
            </section>
        </div>
    </main>
</x-guest-layout>
