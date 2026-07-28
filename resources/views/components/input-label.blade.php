@props(['value'])

<label {{ $attributes->merge(['class' => 'sid-label']) }}>
    {{ $value ?? $slot }}
</label>
