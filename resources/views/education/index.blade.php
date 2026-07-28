<x-app-layout>
    <x-slot name="header">
        <header class="sid-app-bar md:hidden">
            <a href="{{ route('dashboard') }}" class="sid-top-icon" aria-label="Kembali">
                <span class="material-symbols-outlined">arrow_back</span>
            </a>
            <h1 class="flex-1 pr-10 text-center text-[18px] font-semibold text-[#95409e]">Edukasi</h1>
        </header>
    </x-slot>

    <main class="flex flex-1 flex-col gap-5 px-5 pb-8 pt-6">
        <div class="hidden md:block">
            <h1 class="sid-title-main mb-1 text-[var(--color-primary)]">Pusat Edukasi</h1>
            <p class="text-base font-normal text-[#50434e]">Panduan singkat dan ramah untuk mendampingi Ibu selama kehamilan.</p>
        </div>

        @if ($posts->isEmpty())
            <div class="rounded-2xl border border-[#efddef] bg-white p-6 text-center shadow-sm">
                <p class="text-base font-medium text-[#221825]">Belum ada artikel yang cocok</p>
                <p class="mt-2 text-sm font-normal leading-6 text-[#50434e]">Coba kata kunci lain atau publikasikan artikel terlebih dahulu.</p>
            </div>
        @else
            <div class="flex flex-col gap-4">
                @foreach ($posts as $post)
                    <article class="group flex cursor-pointer items-center gap-4 rounded-2xl bg-white p-4 shadow-[0_4px_12px_rgba(149,64,158,0.06)] transition-all duration-300 hover:shadow-[0_8px_24px_rgba(149,64,158,0.12)]">
                        <a href="{{ route('education.show', $post->slug) }}" class="flex w-full items-center gap-4">
                            <div class="h-24 w-24 shrink-0 overflow-hidden rounded-xl bg-[#efddef]">
                                <img src="{{ $post->cover_image_url }}" alt="{{ $post->title }}" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
                            </div>
                            <div class="flex flex-1 flex-col justify-center py-1">
                                <div class="mb-1 flex items-center gap-2">
                                    <span class="rounded px-1 py-[2px] text-[10px] font-medium uppercase tracking-wider
                                        @if ($loop->first) bg-[#ffdad6] text-[#93000a]
                                        @elseif ($loop->iteration === 2) bg-[#f7b5ff] text-[#663170]
                                        @else bg-[#f0dcf3] text-[#231727] @endif">
                                        Artikel
                                    </span>
                                </div>
                                <h2 class="text-sm font-medium text-[#221825] transition-colors group-hover:text-[#95409e]">{{ $post->title }}</h2>
                                <p class="mt-1 text-xs font-normal leading-5 text-[#50434e]">{{ $post->display_excerpt }}</p>
                                <div class="mt-2 flex items-center gap-1 text-[10px] font-medium text-[#82737f]">
                                    <span class="material-symbols-outlined text-[14px]">schedule</span>
                                    <span>{{ $post->published_at?->translatedFormat('d M Y') }}</span>
                                </div>
                            </div>
                        </a>
                    </article>
                @endforeach
            </div>

            <div>
                {{ $posts->links() }}
            </div>
        @endif
    </main>
</x-app-layout>
