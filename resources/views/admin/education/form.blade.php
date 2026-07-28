<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-sm font-medium text-slate-500">Panel Admin SIDEDIKK</p>
                <h1 class="mt-1 text-3xl font-extrabold tracking-tight text-slate-950">{{ $pageTitle }}</h1>
            </div>
            <a href="{{ route('admin.education.index') }}" class="sid-button-secondary">Kembali</a>
        </div>
    </x-slot>

    <div class="space-y-6">
        @include('admin.partials.nav')

        @if (session('status'))
            <div class="rounded-[24px] border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-[var(--color-success)]">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ $formAction }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @if ($formMethod !== 'POST')
                @method($formMethod)
            @endif

            <div class="sid-card p-6">
                <div class="grid gap-4 md:grid-cols-2">
                    <label class="md:col-span-2">
                        <span class="sid-label">Judul artikel</span>
                        <input type="text" name="title" value="{{ old('title', $post->title) }}" class="sid-input" required>
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </label>
                    <label class="md:col-span-2">
                        <span class="sid-label">Ringkasan singkat</span>
                        <textarea name="excerpt" class="sid-input min-h-24" maxlength="500" required>{{ old('excerpt', $post->excerpt) }}</textarea>
                        <x-input-error :messages="$errors->get('excerpt')" class="mt-2" />
                    </label>
                    <label class="md:col-span-2">
                        <span class="sid-label">Isi artikel</span>
                        <textarea name="body" class="sid-input min-h-48" required>{{ old('body', $post->body) }}</textarea>
                        <x-input-error :messages="$errors->get('body')" class="mt-2" />
                    </label>
                    <label>
                        <span class="sid-label">Status</span>
                        <select name="status" class="sid-input">
                            @foreach (\App\Enums\EducationPostStatus::cases() as $status)
                                <option value="{{ $status->value }}" @selected(old('status', $post->status?->value ?? 'draft') === $status->value)>{{ str($status->value)->title() }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-2" />
                    </label>
                    <label>
                        <span class="sid-label">Cover image</span>
                        <input type="file" name="cover_image" accept="image/png,image/jpeg,image/webp" class="sid-input">
                        <x-input-error :messages="$errors->get('cover_image')" class="mt-2" />
                    </label>
                </div>

                @if ($post->cover_image_path)
                    <div class="mt-5">
                        <p class="text-sm font-medium text-slate-500">Cover saat ini</p>
                        <img src="{{ $post->cover_image_path }}" alt="{{ $post->title }}" class="mt-3 h-48 w-full max-w-md rounded-[24px] object-cover">
                    </div>
                @endif
            </div>

            <button type="submit" class="sid-button-primary">Simpan Artikel</button>
        </form>
    </div>
</x-app-layout>
