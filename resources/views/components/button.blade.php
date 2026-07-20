@props(['type' => 'submit', 'variant' => 'primary'])

@php
    $baseClasses = 'inline-flex items-center justify-center px-4 py-2 text-sm font-bold leading-5 transition-all duration-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed shadow-sm active:scale-95';
    
    $variants = [
        'primary' => 'text-white bg-gradient-to-r from-brand-600 to-brand-500 border border-transparent hover:from-brand-500 hover:to-brand-400 focus:ring-brand-500 shadow-brand-500/30 hover:shadow-lg',
        'secondary' => 'text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 hover:text-brand-600 hover:border-gray-300 focus:ring-gray-200',
        'danger' => 'text-white bg-gradient-to-r from-red-600 to-red-500 border border-transparent hover:from-red-500 hover:to-red-400 focus:ring-red-500 shadow-red-500/30 hover:shadow-lg',
    ];
    
    $classes = $baseClasses . ' ' . ($variants[$variant] ?? $variants['primary']);
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</button>
