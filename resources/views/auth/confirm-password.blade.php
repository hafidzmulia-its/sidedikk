<x-guest-layout>
    <div class="px-5 pt-8">
        <h1 class="text-[24px] font-semibold text-[#221825]">Konfirmasi Kata Sandi</h1>
        <p class="mt-2 text-sm text-gray-600">
            Demi keamanan akun, mohon masukkan kembali kata sandi Ibu untuk melanjutkan.
        </p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4 px-5 pt-4">
        @csrf

        <div>
            <x-input-label for="password" value="Kata Sandi" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex justify-end pt-2">
            <x-primary-button>
                Konfirmasi
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
