<section>
    <header>
        <h2 class="text-[18px] font-semibold text-[#1d2f5f]">Informasi Akun</h2>
        <p class="mt-1 text-sm leading-6 text-[#7f90a8]">
            Perbarui identitas dasar dan HPHT agar perhitungan usia kehamilan tetap akurat untuk Ibu.
        </p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="mt-5 space-y-4">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" value="Nama Lengkap" />
            <x-text-input id="name" name="name" type="text" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" name="email" type="email" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <x-input-label for="age" value="Usia Ibu" />
                <x-text-input id="age" name="age" type="number" min="15" max="60" :value="old('age', $user->age)" required />
                <x-input-error :messages="$errors->get('age')" />
            </div>

            <div>
                <x-input-label for="hpht_date" value="HPHT" />
                <x-text-input id="hpht_date" name="hpht_date" type="date" :value="old('hpht_date', optional($user->hpht_date)->format('Y-m-d'))" required />
                <x-input-error :messages="$errors->get('hpht_date')" />
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3 pt-1">
            <x-primary-button>Simpan Perubahan</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm font-medium text-[var(--color-success)]"
                >Data akun berhasil diperbarui.</p>
            @endif
        </div>
    </form>
</section>
