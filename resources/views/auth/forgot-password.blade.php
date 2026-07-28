<x-guest-layout>
        <header class="sid-app-bar">
        <a href="{{ route('home') }}" class="sid-top-icon" aria-label="Kembali">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <h1 class="pointer-events-none absolute left-1/2 -translate-x-1/2 text-[18px] font-semibold text-[var(--color-primary)]">Lupa Kata Sandi</h1>
        <div class="h-10 w-10"></div>
    </header>
    <div class="px-5 pt-8">
        <h1 class="text-[24px] font-semibold text-[#221825]">Lupa Kata Sandi</h1>
        <p class="mt-2 text-sm text-gray-600">
            Masukkan email akun Ibu. Kami akan mengirim tautan untuk membuat kata sandi baru.
        </p>
    </div>

    <div class="px-5 pt-4">
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf

            <div>
                <x-input-label for="email" value="Email" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="flex items-center justify-end pt-2">
                <x-primary-button>
                    Kirim Tautan Reset
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>
