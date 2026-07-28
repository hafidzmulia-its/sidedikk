<button {{ $attributes->merge(['type' => 'submit', 'class' => 'sid-button-primary']) }}>
    {{ $slot }}
</button>
