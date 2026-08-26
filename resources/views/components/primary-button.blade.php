<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-mental-400 hover:bg-mental-500 border border-transparent rounded-xl font-semibold text-sm text-white transition-colors duration-200']) }}>
    {{ $slot }}
</button>
