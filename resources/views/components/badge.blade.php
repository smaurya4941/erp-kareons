@props(['type' => 'primary'])

@php
    $variants = [
        'primary' => 'bg-blue-100 text-blue-700',
        'success' => 'bg-green-100 text-green-700',
        'danger' => 'bg-red-100 text-red-700',
        'warning' => 'bg-yellow-100 text-yellow-800',
        'gray' => 'bg-gray-100 text-gray-700',
    ];
    
    $classes = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' . ($variants[$type] ?? $variants['primary']);
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>
