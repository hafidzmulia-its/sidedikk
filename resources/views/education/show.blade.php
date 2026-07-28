<x-app-layout>
    <x-slot name="header">
        <header class="sid-app-bar border-b border-[#efddef]">
            <a href="{{ route('education.index') }}" class="sid-top-icon" aria-label="Kembali">
                <span class="material-symbols-outlined">arrow_back</span>
            </a>
            <h1 class="flex-1 pr-10 text-center text-[18px] font-semibold text-[#95409e]">Edukasi</h1>
        </header>
    </x-slot>

    <main class="space-y-5 px-5 pb-8 pt-6">
        <article class="overflow-hidden rounded-2xl bg-white shadow-[0_4px_12px_rgba(149,64,158,0.06)]">
            <img src="{{ $post->cover_image_url }}" alt="{{ $post->title }}" class="h-auto w-full object-cover">

            <div class="space-y-5 p-6">
                <div class="flex flex-wrap items-center gap-3">
                    <span class="rounded-full bg-[#f2ddf6] px-3 py-1 text-xs font-semibold text-[#95409e]">Artikel Edukasi</span>
                    <span class="text-xs font-medium text-slate-400">{{ $post->published_at?->translatedFormat('d F Y') }}</span>
                </div>

                <h1 class="text-[22px] font-semibold leading-[30px] text-[#221825]">{{ $post->title }}</h1>
                <div class="rounded-[20px] border border-[#f0e2f3] bg-[#fffafd] p-4">
                    <p class="text-sm leading-7 text-slate-600">{{ $post->display_excerpt }}</p>
                </div>

                <div class="space-y-4 text-sm leading-7 text-slate-700">
                    @foreach ($post->display_body_blocks as $block)
                        @if ($block['type'] === 'list')
                            <section class="rounded-[22px] border border-[#f0e2f3] bg-white p-4 shadow-[0_8px_24px_rgba(149,64,158,0.04)]">
                                @if (! empty($block['title']))
                                    <h2 class="text-sm font-semibold text-[#221825]">{{ $block['title'] }}</h2>
                                @endif

                                <ul class="{{ ! empty($block['title']) ? 'mt-3' : '' }} space-y-3">
                                    @foreach ($block['items'] as $item)
                                        <li class="flex items-start gap-3">
                                            <span class="mt-2 h-2.5 w-2.5 flex-shrink-0 rounded-full bg-[var(--color-primary)]"></span>
                                            <span class="flex-1 leading-7 text-slate-700">{{ $item }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </section>
                        @else
                            <div class="rounded-[22px] border border-[#f0e2f3] bg-white p-4 shadow-[0_8px_24px_rgba(149,64,158,0.04)]">
                                {!! nl2br(e($block['text'] ?? '')) !!}
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </article>

        <aside class="space-y-4">
            <div class="rounded-2xl bg-white p-5 shadow-[0_4px_12px_rgba(149,64,158,0.06)]">
                <p class="text-base font-medium text-slate-900">Artikel Lainnya</p>
                <div class="mt-4 space-y-4">
                    @forelse ($relatedPosts as $relatedPost)
                        <a href="{{ route('education.show', $relatedPost->slug) }}" class="block rounded-[20px] border bg-white p-4 transition duration-200 hover:-translate-y-0.5 hover:shadow-[var(--shadow-soft)]">
                            <p class="font-medium text-slate-900">{{ $relatedPost->title }}</p>
                            <p class="mt-2 text-sm leading-6 text-slate-500">{{ $relatedPost->display_excerpt }}</p>
                        </a>
                    @empty
                        <p class="text-sm text-slate-500">Belum ada artikel lain.</p>
                    @endforelse
                </div>
            </div>
        </aside>
    </main>
</x-app-layout>
