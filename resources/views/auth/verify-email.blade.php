<x-guest-layout>
    <div class="px-5 pt-8">
        <h1 class="text-[24px] font-semibold text-[#221825]">Verifikasi Email</h1>
        <p class="mt-2 text-sm text-gray-600">
            Sebelum mulai menggunakan SIDEDIKK, mohon verifikasi alamat email Ibu melalui tautan yang telah kami kirimkan.
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="px-5 pt-4 font-medium text-sm text-green-600">
            Tautan verifikasi baru telah dikirim ke email yang Ibu daftarkan.
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between px-5">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button>
                    Kirim Ulang Verifikasi
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Keluar
            </button>
        </form>
    </div>
</x-guest-layout>
