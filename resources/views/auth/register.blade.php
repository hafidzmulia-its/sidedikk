<x-guest-layout>
    <header class="sid-app-bar">
        <a href="{{ route('home') }}" class="sid-top-icon" aria-label="Kembali">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <h1 class="pointer-events-none absolute left-1/2 -translate-x-1/2 text-[18px] font-semibold text-[var(--color-primary)]">Daftar</h1>
        <div class="h-10 w-10"></div>
    </header>

    <main class="relative z-0 mx-auto flex min-h-[calc(100vh-56px)] w-full max-w-md flex-col px-5 py-6 pb-24">
        <div class="mb-6 text-center">
            <img src="{{ asset('brand/icon-512.png') }}" alt="SIDEDIKK" class="mx-auto mb-4 h-24 w-24 rounded-full bg-white p-2 object-cover shadow-sm">
            <h2 class="sid-title-main mb-2 text-[var(--color-primary)]">Selamat Datang, Ibu</h2>
            <p class="text-sm font-normal text-[#50434e]">Mari mulai perjalanan kehamilan yang sehat dan aman bersama SIDEDIKK.</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="flex flex-col gap-4">
            @csrf

            <div class="flex flex-col gap-1">
                <label for="name" class="ml-1 text-xs font-medium text-[#221825]">Nama Lengkap</label>
                <div class="relative">
                    <span class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[#82737f]">person</span>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Masukkan nama lengkap" class="sid-field pl-10">
                </div>
                <x-input-error :messages="$errors->get('name')" />
            </div>

            <div class="flex flex-col gap-1">
                <label for="email" class="ml-1 text-xs font-medium text-[#221825]">Email</label>
                <div class="relative">
                    <span class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[#82737f]">mail</span>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="username" placeholder="contoh@email.com" class="sid-field pl-10">
                </div>
                <x-input-error :messages="$errors->get('email')" />
            </div>

            <div class="flex flex-col gap-1">
                <label for="password" class="ml-1 text-xs font-medium text-[#221825]">Kata Sandi</label>
                <div class="relative">
                    <span class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[#82737f]">lock</span>
                    <input id="password" name="password" type="password" required autocomplete="new-password" placeholder="Minimal 8 karakter" class="sid-field pl-10 pr-10">
                    <button type="button" data-toggle-password="password" class="absolute right-3 top-1/2 -translate-y-1/2 text-[#82737f] transition-colors hover:text-[#95409e]">
                        <span class="material-symbols-outlined text-[20px]">visibility_off</span>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" />
            </div>

            <div class="flex flex-col gap-1">
                <label for="password_confirmation" class="ml-1 text-xs font-medium text-[#221825]">Konfirmasi Kata Sandi</label>
                <div class="relative">
                    <span class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[#82737f]">lock_reset</span>
                    <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" placeholder="Ulangi password" class="sid-field pl-10">
                    <button type="button" data-toggle-password="password_confirmation" class="absolute right-3 top-1/2 -translate-y-1/2 text-[#82737f] transition-colors hover:text-[#95409e]">
                        <span class="material-symbols-outlined text-[20px]">visibility_off</span>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password_confirmation')" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="flex flex-col gap-1">
                    <label for="age" class="ml-1 text-xs font-medium text-[#221825]">Usia Ibu</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-xs text-[#82737f]">Tahun</span>
                        <input id="age" name="age" type="number" min="15" max="60" value="{{ old('age') }}" required placeholder="" class="sid-field pr-14 text-center">
                    </div>
                    <x-input-error :messages="$errors->get('age')" />
                </div>

                <div class="flex flex-col gap-1">
                    <label for="hpht_date" class="ml-1 text-xs font-medium text-[#221825]">Perkiraan HPHT</label>
                    <input id="hpht_date" name="hpht_date" type="date" value="{{ old('hpht_date') }}" required class="sid-field px-4 text-center">
                    <x-input-error :messages="$errors->get('hpht_date')" />
                </div>
            </div>

            <p class="sid-helper -mt-1 px-1">Usia kehamilan akan dihitung dari HPHT yang diisi.</p>

            <label class="mt-1 flex items-start gap-2 px-1">
                <input type="checkbox" name="privacy_consent" value="1" class="mt-1 h-4 w-4 rounded border-[#d3c1d0] text-[var(--color-primary)] focus:ring-[var(--color-primary)]" @checked(old('privacy_consent'))>
                <span class="text-xs font-normal leading-5 text-[#50434e]">Saya menyetujui kebijakan privasi SIDEDIKK dan memahami data saya digunakan untuk proses skrining.</span>
            </label>
            <x-input-error :messages="$errors->get('privacy_consent')" />

            <label class="flex items-start gap-2 px-1">
                <input type="checkbox" name="medical_disclaimer_consent" value="1" class="mt-1 h-4 w-4 rounded border-[#d3c1d0] text-[var(--color-primary)] focus:ring-[var(--color-primary)]" @checked(old('medical_disclaimer_consent'))>
                <span class="text-xs font-normal leading-5 text-[#50434e]">Saya memahami bahwa SIDEDIKK adalah alat skrining awal dan bukan diagnosis medis.</span>
            </label>
            <x-input-error :messages="$errors->get('medical_disclaimer_consent')" />

            <div class="mt-4 flex flex-col items-center gap-4">
                <button type="submit" class="w-full rounded-full bg-[#95409e] px-6 py-4 text-sm font-semibold text-white shadow-[0_4px_12px_rgba(149,64,158,0.2)] transition-all hover:opacity-90 active:scale-95">
                    Daftar
                </button>
                <p class="text-sm font-normal text-[#50434e]">
                    Sudah punya akun? <a href="{{ route('login') }}" class="font-semibold text-[#95409e] hover:underline">Masuk</a>
                </p>
            </div>
        </form>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('[data-toggle-password]').forEach((button) => {
                button.addEventListener('click', () => {
                    const input = document.getElementById(button.dataset.togglePassword);
                    const icon = button.querySelector('.material-symbols-outlined');
                    const isPassword = input.type === 'password';

                    input.type = isPassword ? 'text' : 'password';
                    icon.textContent = isPassword ? 'visibility' : 'visibility_off';
                });
            });
        });
    </script>
</x-guest-layout>
