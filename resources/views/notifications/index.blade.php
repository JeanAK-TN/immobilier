<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-900">{{ __('Notifications') }}</h2>
                <p class="mt-1 text-sm text-gray-500">{{ __('Historique complet de vos alertes et activités.') }}</p>
            </div>

            @if ($notifications->total() > 0)
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    <button
                        type="submit"
                        class="inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition hover:bg-gray-50"
                    >
                        {{ __('Tout marquer comme lu') }}
                    </button>
                </form>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-3xl space-y-5 px-4 sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3.5 text-sm font-medium text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            @if ($notifications->isEmpty())
                <section class="rounded-2xl border border-dashed border-gray-300 bg-white p-12 text-center shadow-sm">
                    <p class="text-4xl text-gray-300">🔔</p>
                    <h3 class="mt-4 text-lg font-semibold text-gray-900">{{ __('Aucune notification') }}</h3>
                    <p class="mt-2 text-sm text-gray-500">{{ __('Les nouvelles activités s\'afficheront ici.') }}</p>
                </section>
            @else
                <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                    <ul class="divide-y divide-gray-100">
                        @foreach ($notifications as $notif)
                            @php $estLue = (bool) $notif->read_at; @endphp
                            <li>
                                <form method="POST" action="{{ route('notifications.read', $notif->id) }}">
                                    @csrf
                                    <button type="submit"
                                            class="flex w-full items-start gap-4 px-5 py-4 text-left transition hover:bg-gray-50">
                                        <span @class([
                                            'mt-1.5 inline-block h-2 w-2 shrink-0 rounded-full',
                                            'bg-blue-500' => ! $estLue,
                                            'bg-gray-200' => $estLue,
                                        ])></span>
                                        <div class="min-w-0 flex-1">
                                            <p @class([
                                                'text-sm leading-snug',
                                                'font-semibold text-gray-900' => ! $estLue,
                                                'text-gray-700' => $estLue,
                                            ])>
                                                {{ $notif->data['message'] ?? __('Notification') }}
                                            </p>
                                            <p class="mt-1 text-xs text-gray-400">
                                                {{ $notif->created_at->translatedFormat('d F Y à H:i') }} · {{ $notif->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                        <svg class="h-4 w-4 shrink-0 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </button>
                                </form>
                            </li>
                        @endforeach
                    </ul>
                </section>

                <div>{{ $notifications->links() }}</div>
            @endif

        </div>
    </div>
</x-app-layout>
