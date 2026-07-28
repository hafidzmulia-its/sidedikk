@props([
    'message' => 'SIDEDIKK merupakan alat skrining awal dan bukan diagnosis medis. Hasil aplikasi tidak menggantikan pemeriksaan tenaga kesehatan. Segera hubungi fasilitas kesehatan apabila mengalami keluhan berat atau kondisi darurat.',
])

<div {{ $attributes->merge(['class' => 'rounded-2xl border border-[#efddef] bg-white p-4 text-left']) }}>
    <div class="flex items-start gap-3">
        <span class="material-symbols-outlined mt-0.5 text-[#82737f]">warning</span>
        <p class="text-xs font-normal leading-5 text-[#50434e]">
            {{ $message }}
        </p>
    </div>
</div>
