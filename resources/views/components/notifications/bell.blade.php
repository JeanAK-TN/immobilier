@props([
    'count' => 0,
    'recentes' => collect(),
])

<x-dropdown align="right" width="80">
    <x-slot name="trigger">
        <button class="relative inline-flex h-9 w-9 items-center justify-center rounded-full text-gray-500 transition hover:bg-gray-100 hover:text-gray-700">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
            </svg>
            @if ($count > 0)
                <span class="absolute -top-0.5 -right-0.5 inline-flex min-w-[18px] items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold text-white ring-2 ring-white">
                    {{ $count > 9 ? '9+' : $count }}
                </span>
            @endif
        </button>
    </x-slot>

    <x-slot name="content">
        <div class="w-80">
            <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3">
                <p class="text-sm font-semibold text-gray-900">{{ __('Notifications') }}</p>
                @if ($count > 0)
                    <form method="POST" action="{{ route('notifications.read-all') }}">
                        @csrf
                        <button type="submit" class="text-xs font-medium text-gray-500 transition hover:text-gray-700">
                            {{ __('Tout marquer lu') }}
                        </button>
                    </form>
                @endif
            </div>

            @if ($recentes->isEmpty())
                <div class="px-4 py-10 text-center text-sm text-gray-400">
                    {{ __('Aucune notification.') }}
                </div>
            @else
                <ul class="max-h-96 divide-y divide-gray-100 overflow-y-auto">
                    @foreach ($recentes as $notif)
                        @php $estLue = (bool) $notif->read_at; @endphp
                        <li>
                            <form method="POST" action="{{ route('notifications.read', $notif->id) }}">
                                @csrf
                                <button type="submit"
                                        class="flex w-full items-start gap-3 px-4 py-3 text-left transition hover:bg-gray-50">
                                    <span @class([
                                        'mt-1.5 inline-block h-2 w-2 shrink-0 rounded-full',
                                        'bg-blue-500' => ! $estLue,
                                        'bg-gray-200' => $estLue,
                                    ])></span>
                                    <div class="min-w-0 flex-1">
                                        <p @class([
                                            'text-sm leading-snug',
                                            'font-semibold text-gray-900' => ! $estLue,
                                            'text-gray-600' => $estLue,
                                        ])>
                                            {{ $notif->data['message'] ?? __('Notification') }}
                                        </p>
                                        <p class="mt-0.5 text-xs text-gray-400">
                                            {{ $notif->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </button>
                            </form>
                        </li>
                    @endforeach
                </ul>

                <div class="border-t border-gray-100 px-4 py-2 text-center">
                    <a href="{{ route('notifications.index') }}" class="text-xs font-medium text-gray-500 transition hover:text-gray-700">
                        {{ __('Voir toutes les notifications') }}
                    </a>
                </div>
            @endif
        </div>
    </x-slot>
</x-dropdown>
