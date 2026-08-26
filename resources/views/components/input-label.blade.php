@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-semibold text-sm text-warm-700']) }}>
    {{ $value ?? $slot }}
</label>
