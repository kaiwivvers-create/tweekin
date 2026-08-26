@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-2 border-warm-200 focus:border-mental-400 focus:ring-0 rounded-xl bg-white text-warm-800 placeholder-warm-400 transition-colors']) }}>
