<x-app-layout>
    <x-slot name="header">
        <x-admin.page-header
            :title="$pageTitle"
            description="Susun level risiko, rentang skor, deskripsi, dan rekomendasi dengan tata letak admin yang seragam."
        >
            <a href="{{ route('admin.risk-rules.index') }}" class="sid-button-secondary">Kembali</a>
        </x-admin.page-header>
    </x-slot>

    @php
        $riskLevelRows = old('risk_levels', $riskLevels);
    @endphp

    <div class="space-y-6">
        @include('admin.partials.nav')

        <form
            method="POST"
            action="{{ $formAction }}"
            x-data='{
                riskLevels: @json(array_values($riskLevelRows)),
                addLevel() {
                    const lastLevel = this.riskLevels[this.riskLevels.length - 1];
                    const nextMin = lastLevel ? Number(lastLevel.max_score || 0) + 1 : 0;
                    this.riskLevels.push({
                        name: "",
                        slug: "",
                        min_score: nextMin,
                        max_score: nextMin,
                        semantic_color: "success",
                        description: "DEMO DATA - NOT FOR MEDICAL USE",
                        recommendation: "DEMO DATA - NOT FOR MEDICAL USE",
                        is_active: true
                    });
                },
                removeLevel(index) {
                    if (this.riskLevels.length > 1) {
                        this.riskLevels.splice(index, 1);
                    }
                }
            }'
            class="space-y-6"
        >
            @csrf
            @if ($formMethod !== 'POST')
                @method($formMethod)
            @endif

            <div class="sid-card p-6">
                <div class="grid gap-4 md:grid-cols-2">
                    <label>
                        <span class="sid-label">Judul versi</span>
                        <input type="text" name="title" value="{{ old('title', $version->title) }}" class="sid-input" required>
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </label>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <label class="flex items-center gap-3 rounded-[24px] border bg-white px-4 py-3">
                            <input type="checkbox" name="is_demo_data" value="1" @checked(old('is_demo_data', $version->is_demo_data ?? true)) class="h-5 w-5 text-[var(--color-primary)]">
                            <span class="text-sm font-semibold text-slate-700">Tandai demo data</span>
                        </label>
                        <label class="flex items-center gap-3 rounded-[24px] border bg-white px-4 py-3">
                            <input type="checkbox" name="medical_approval_required" value="1" @checked(old('medical_approval_required', $version->medical_approval_required ?? true)) class="h-5 w-5 text-[var(--color-primary)]">
                            <span class="text-sm font-semibold text-slate-700">Butuh approval medis</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="sid-card p-6">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-lg font-bold text-slate-900">Level Risiko</p>
                        <p class="mt-1 text-sm text-slate-500">Rentang wajib berurutan dari 0 tanpa celah.</p>
                    </div>
                    <button type="button" class="sid-button-secondary" @click="addLevel()">Tambah Level</button>
                </div>

                <x-input-error :messages="$errors->get('risk_levels')" class="mt-3" />

                <div class="mt-5 space-y-4">
                    <template x-for="(level, index) in riskLevels" :key="index">
                        <div class="rounded-[24px] border bg-white p-5">
                            <div class="flex items-center justify-between gap-4">
                                <p class="text-sm font-semibold text-slate-500" x-text="`Level ${index + 1}`"></p>
                                <button type="button" class="text-sm font-semibold text-[var(--color-danger)]" @click="removeLevel(index)">Hapus</button>
                            </div>

                            <div class="mt-4 grid gap-4">
                                <div class="grid gap-4 md:grid-cols-2">
                                    <label>
                                        <span class="sid-label">Nama level</span>
                                        <input x-model="level.name" :name="`risk_levels[${index}][name]`" type="text" class="sid-input" required>
                                    </label>
                                    <label>
                                        <span class="sid-label">Slug</span>
                                        <input x-model="level.slug" :name="`risk_levels[${index}][slug]`" type="text" class="sid-input" required>
                                    </label>
                                </div>

                                <div class="grid gap-4 md:grid-cols-3">
                                    <label>
                                        <span class="sid-label">Skor minimum</span>
                                        <input x-model="level.min_score" :name="`risk_levels[${index}][min_score]`" type="number" min="0" class="sid-input" required>
                                    </label>
                                    <label>
                                        <span class="sid-label">Skor maksimum</span>
                                        <input x-model="level.max_score" :name="`risk_levels[${index}][max_score]`" type="number" min="0" class="sid-input" required>
                                    </label>
                                    <label>
                                        <span class="sid-label">Warna semantik</span>
                                        <select x-model="level.semantic_color" :name="`risk_levels[${index}][semantic_color]`" class="sid-input">
                                            <option value="success">Success</option>
                                            <option value="warning">Warning</option>
                                            <option value="danger">Danger</option>
                                            <option value="info">Info</option>
                                            <option value="primary">Primary</option>
                                        </select>
                                    </label>
                                </div>

                                <label>
                                    <span class="sid-label">Deskripsi</span>
                                    <textarea x-model="level.description" :name="`risk_levels[${index}][description]`" class="sid-input min-h-24" required></textarea>
                                </label>
                                <label>
                                    <span class="sid-label">Rekomendasi</span>
                                    <textarea x-model="level.recommendation" :name="`risk_levels[${index}][recommendation]`" class="sid-input min-h-24" required></textarea>
                                </label>
                                <label class="flex items-center gap-3 rounded-[24px] border bg-white px-4 py-3">
                                    <input type="checkbox" value="1" :name="`risk_levels[${index}][is_active]`" x-model="level.is_active" class="h-5 w-5 text-[var(--color-primary)]">
                                    <span class="text-sm font-semibold text-slate-700">Aktif</span>
                                </label>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <div class="flex flex-wrap gap-3">
                <button type="submit" class="sid-button-primary">Simpan Draft</button>
                @if ($version->exists)
                    <a href="{{ route('admin.risk-rules.show', $version) }}" class="sid-button-secondary">Batal</a>
                @endif
            </div>
        </form>
    </div>
</x-app-layout>
