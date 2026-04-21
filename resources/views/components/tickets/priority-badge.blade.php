@props(['priority'])

@php
    [$background, $text, $ring] = match ($priority->value) {
        'haute' => ['bg-rose-50', 'text-rose-700', 'ring-rose-200'],
        'moyenne' => ['bg-orange-50', 'text-orange-700', 'ring-orange-200'],
        'basse' => ['bg-teal-50', 'text-teal-700', 'ring-teal-200'],
        default => ['bg-gray-100', 'text-gray-700', 'ring-gray-200'],
    };
@endphp

<span {{ $attributes->class(["inline-flex items-center rounded-full px-3 py-1 text-xs font-medium ring-1 ring-inset {$background} {$text} {$ring}"]) }}>
    {{ $priority->label() }}
</span>
