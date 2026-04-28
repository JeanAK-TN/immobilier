@props(['active' => false])

@php
$base = 'group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition';
$classes = ($active ?? false)
    ? $base.' bg-blue-50 text-blue-700'
    : $base.' text-gray-600 hover:bg-gray-50 hover:text-gray-900';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
