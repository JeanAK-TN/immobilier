@props(['status'])

@php
    [$background, $text, $ring] = match ($status->value) {
        'ouvert' => ['bg-sky-50', 'text-sky-700', 'ring-sky-200'],
        'en_cours' => ['bg-amber-50', 'text-amber-800', 'ring-amber-200'],
        'en_attente_locataire' => ['bg-violet-50', 'text-violet-700', 'ring-violet-200'],
        'resolu' => ['bg-emerald-50', 'text-emerald-700', 'ring-emerald-200'],
        'ferme' => ['bg-gray-100', 'text-gray-700', 'ring-gray-200'],
        default => ['bg-gray-100', 'text-gray-700', 'ring-gray-200'],
    };
@endphp

<span {{ $attributes->class(["inline-flex items-center rounded-full px-3 py-1 text-xs font-medium ring-1 ring-inset {$background} {$text} {$ring}"]) }}>
    {{ $status->label() }}
</span>
