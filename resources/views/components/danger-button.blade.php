<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center rounded-xl bg-[var(--color-danger)] px-5 py-3 text-sm font-semibold text-white transition duration-200 hover:brightness-105 focus:outline-none focus:ring-2 focus:ring-[rgba(200,62,80,0.3)] focus:ring-offset-2']) }}>
    {{ $slot }}
</button>
