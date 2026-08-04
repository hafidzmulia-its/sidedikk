<x-app-layout>
    <!-- <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-slate-500">Panel Admin SIDEDIKK</p>
            <h1 class="mt-1 text-[2.25rem] font-extrabold leading-[1.05] tracking-tight text-slate-950 sm:text-3xl">Ringkasan Operasional</h1>
        </div>
    </x-slot> -->

    <x-slot name="header">
        <x-admin.page-header
            title="Ringkasan Operasional"
            description="Pantau pengguna, screening, pertanyaan aktif, dan artikel dari satu dashboard admin yang konsisten."
        />
    </x-slot>
    <div class="space-y-6">
        @include('admin.partials.nav')

        <div class="grid grid-cols-2 gap-4">
            <div class="sid-card p-5">
                <p class="text-sm font-medium text-slate-500">Pengguna</p>
                <p class="mt-3 text-3xl font-extrabold text-slate-950">{{ $stats['users'] }}</p>
            </div>
            <div class="sid-card p-5">
                <p class="text-xs font-medium text-slate-500">Screening Selesai</p>
                <p class="mt-3 text-3xl font-extrabold text-slate-950">{{ $stats['completed_screenings'] }}</p>
            </div>
            <!-- <div class="sid-card p-5">
                <p class="text-sm font-medium text-slate-500">Jumlah Pertanyaan</p>
                <p class="mt-3 text-3xl font-extrabold text-slate-950">{{ $stats['questions'] }}</p>
            </div> -->
            <div class="sid-card p-5">
                <p class="text-sm font-medium text-slate-500">Pertanyaan Aktif</p>
                <p class="mt-3 text-3xl font-extrabold text-slate-950">{{ $stats['active_questions'] }}</p>
            </div>
            <div class="sid-card p-5">
                <p class="text-sm font-medium text-slate-500">Artikel</p>
                <p class="mt-3 text-3xl font-extrabold text-slate-950">{{ $stats['education_posts'] }}</p>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-2">
            <div class="sid-card p-6">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-lg font-bold text-slate-900">Distribusi Risiko</p>
                        <p class="mt-1 text-sm text-slate-500">Kategori hasil screening selesai.</p>
                    </div>
                </div>

                <div class="mt-6 h-64 sm:h-72">
                    <canvas
                        data-admin-chart="doughnut"
                        data-chart-labels='@json($riskDistributionChart["labels"])'
                        data-chart-values='@json($riskDistributionChart["values"])'
                    ></canvas>
                </div>
            </div>

            <div class="sid-card p-6">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-lg font-bold text-slate-900">Tren 14 Hari</p>
                        <p class="mt-1 text-sm text-slate-500">Jumlah screening selesai per hari.</p>
                    </div>
                </div>

                <div class="mt-6 h-64 sm:h-72">
                    <canvas
                        data-admin-chart="line"
                        data-chart-labels='@json($trendChart["labels"])'
                        data-chart-values='@json($trendChart["values"])'
                    ></canvas>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
