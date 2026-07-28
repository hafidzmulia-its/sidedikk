<x-guest-layout>
    <header class="sid-app-bar">
        <a href="{{ route('home') }}" class="sid-top-icon" aria-label="Kembali">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <h1 class="pointer-events-none absolute left-1/2 -translate-x-1/2 text-[18px] font-semibold text-[var(--color-primary)]">Masuk</h1>
        <div class="h-10 w-10"></div>
    </header>

    <main class="flex min-h-[calc(100vh-56px)] flex-col px-5 py-6">
        <!-- <div class="mb-8 mt-4 text-center md:text-left">
            <h1 class="sid-title-main mb-2">Masuk</h1>
            <p class="text-sm font-normal text-[#50434e]">Masuk untuk melanjutkan screening dan melihat riwayat Anda.</p>
        </div> -->
        <div class="mb-6 text-center">
            <img src="{{ asset('brand/icon-512.png') }}" alt="SIDEDIKK" class="mx-auto mb-4 h-24 w-24 rounded-full bg-white p-2 object-cover shadow-sm">
            <h2 class="sid-title-main mb-2 text-[var(--color-primary)]">Selamat Datang, Ibu</h2>
            <p class="text-sm font-normal text-[#50434e]">Mari mulai perjalanan kehamilan yang sehat dan aman bersama SIDEDIKK.</p>
        </div>

        <!-- <div class="mb-8 flex justify-center opacity-90">
            <img src="{{ asset('brand/icon-512.png') }}" alt="SIDEDIKK" class="h-48 w-48 rounded-full object-cover shadow-sm">
        </div> -->

        <x-auth-session-status :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="mb-8 flex flex-1 flex-col gap-4">
            @csrf

            <div class="flex flex-col gap-1">
                <label for="email" class="ml-1 text-xs font-medium text-[#221825]">Email</label>
                <div class="relative">
                    <span class="material-symbols-outlined pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-[#82737f]">mail</span>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="contoh@email.com" class="h-14 w-full rounded-xl border border-[#d3c1d0] bg-white pl-12 pr-4 text-sm font-normal text-[#221825] shadow-sm focus:border-[#95409e] focus:outline-none focus:ring-2 focus:ring-[#95409e]/20">
                </div>
                <x-input-error :messages="$errors->get('email')" />
            </div>

            <div class="flex flex-col gap-1">
                <label for="password" class="ml-1 text-xs font-medium text-[#221825]">Kata Sandi</label>
                <div class="relative">
                    <span class="material-symbols-outlined pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-[#82737f]">lock</span>
                    <input id="password" name="password" type="password" required autocomplete="current-password" placeholder="Masukkan kata sandi" class="h-14 w-full rounded-xl border border-[#d3c1d0] bg-white pl-12 pr-12 text-sm font-normal text-[#221825] shadow-sm focus:border-[#95409e] focus:outline-none focus:ring-2 focus:ring-[#95409e]/20">
                    <button type="button" data-toggle-password="password" class="absolute right-4 top-1/2 -translate-y-1/2 text-[#82737f] transition-colors hover:text-[#95409e]">
                        <span class="material-symbols-outlined">visibility_off</span>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" />
            </div>

            @if (Route::has('password.request'))
                <div class="-mt-2 flex justify-end">
                    <a href="{{ route('password.request') }}" class="text-xs font-medium text-[#95409e] hover:opacity-80">Lupa kata sandi?</a>
                </div>
            @endif

            <div class="mt-auto flex flex-col gap-4 pb-4 pt-6">
                <button type="submit" class="flex h-14 w-full items-center justify-center rounded-full bg-[#95409e] text-sm font-semibold text-white shadow-md transition-all hover:opacity-90 active:scale-[0.98]">
                    Masuk
                </button>
                <div class="text-center">
                    <p class="text-xs font-normal text-[#50434e]">
                        Belum punya akun? <a href="{{ route('register') }}" class="font-bold text-[#95409e] hover:underline">Daftar</a>
                    </p>
                </div>
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
