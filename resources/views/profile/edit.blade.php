<x-app-layout>
    <x-slot name="header">
        <header class="sid-app-bar">
            <a href="{{ route('dashboard') }}" class="sid-top-icon" aria-label="Kembali">
                <span class="material-symbols-outlined">arrow_back</span>
            </a>
            <h1 class="text-[18px] font-semibold text-[#95409e]">Akun</h1>
            <div class="h-6 w-6"></div>
        </header>
    </x-slot>

    @php
        $applicationInfo = [
            ['label' => 'Versi', 'value' => '1.0.0'],
            ['label' => 'Platform', 'value' => 'Progressive Web App'],
            ['label' => 'Institusi', 'value' => 'Poltekkes Kemenkes Surabaya'],
            ['label' => 'Penulis Aplikasi', 'value' => 'Kharisma Kusumaningtyas'],
        ];
    @endphp

    <main class="space-y-5 px-5 pb-8">
        <div class="sid-card p-5">
            @include('profile.partials.update-profile-information-form')
        </div>

        <div class="sid-card p-5">
            @include('profile.partials.update-password-form')
        </div>

        <form method="POST" action="{{ route('logout') }}" class="sid-card p-5">
            @csrf
            <button type="submit" class="flex w-full items-center gap-4 text-left">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-[#ffe4e7] text-[#ff2f43]">
                    <span class="material-symbols-outlined text-[28px]">logout</span>
                </div>
                <div>
                    <p class="text-[16px] font-medium text-[#ff2f43]">Keluar</p>
                    <p class="mt-1 text-sm font-normal text-[#ff6b78]">Keluar dari akun Anda</p>
                </div>
            </button>
        </form>

        <section class="space-y-2 pt-1">
            <p class="mb-3 text-sm font-semibold uppercase tracking-[0.08em] text-[#9eb0c8]">Tentang Aplikasi</p>
            <div class="sid-card p-5">
                <div class="flex items-start gap-4">
                    <img src="{{ asset('brand/icon-512.png') }}" alt="SIDEDIKK" class="h-16 w-16 rounded-2xl border border-[#e6eef7] bg-white object-cover p-1 shadow-sm">
                    <div class="space-y-0.5">
                        <p class="text-[19px] font-semibold leading-7 text-[#1d2f5f]">SIDEDIKK</p>
                        <p class="text-[15px] font-normal leading-6 text-[#7f90a8]">Deteksi Dini Komplikasi Kehamilan</p>
                    </div>
                </div>

                <p class="mt-5 text-[15px] font-normal leading-7 text-[#31466f]">
                    SIDEDIKK membantu Ibu hamil melakukan skrining awal, memantau usia kehamilan, dan menyimpan riwayat hasil secara lebih rapi dan mudah diakses.
                </p>

                <div class="mt-5 space-y-2.5 border-t border-[#edf2f7] pt-4 text-[13px] text-[#90a0b6]">
                    @foreach ($applicationInfo as $item)
                        <div class="flex items-center justify-between gap-3">
                            <span class="whitespace-nowrap">{{ $item['label'] }}</span>
                            <span class="whitespace-nowrap text-right text-[12px] font-semibold leading-5 text-[#1d2f5f] sm:text-[13px]">{{ $item['value'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="pb-2 pt-1 text-center text-sm leading-6 text-[#9aaac2]">
            <p>&copy; 2026 SIDEDIKK · All rights reserved</p>
            <p>
                Powered by
                <a href="https://nechcode.id" target="_blank" rel="noopener noreferrer" class="font-semibold text-[#0aa7a1] hover:underline">
                    NechCode
                </a>
            </p>
        </section>
    </main>
</x-app-layout>
