<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-900">{{ __('Tickets de maintenance') }}</h2>
                <p class="mt-1 text-sm text-gray-500">{{ __('Suivez et traitez les incidents remontés par vos locataires.') }}</p>
            </div>
            @if ($tickets->total() > 0)
                <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1 text-sm font-semibold text-slate-600">
                    {{ $tickets->total() }} ticket{{ $tickets->total() > 1 ? 's' : '' }}
                </span>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-5 px-4 sm:px-6 lg:px-8">

            @php
                $autresFiltres = array_filter([
                    'recherche' => $filtres['recherche'],
                    'priorite' => $filtres['priorite'],
                    'categorie' => $filtres['categorie'],
                    'bien_id' => $filtres['bienId'],
                    'locataire_id' => $filtres['locataireId'],
                ]);
            @endphp

            {{-- Onglets statut --}}
            <div class="flex flex-wrap gap-1.5">
                <a
                    href="{{ route('proprietaire.tickets.index', $autresFiltres) }}"
                    @class([
                        'rounded-full px-3 py-1.5 text-xs font-semibold transition',
                        'bg-gray-900 text-white' => $filtres['statut'] === '',
                        'bg-gray-100 text-gray-600 hover:bg-gray-200' => $filtres['statut'] !== '',
                    ])
                >{{ __('Tous') }}
                    @if ($tickets->total() > 0 && $filtres['statut'] === '')
                        <span class="ml-1 opacity-60">{{ $tickets->total() }}</span>
                    @endif
                </a>
                @foreach ($statutOptions as $opt)
                    <a
                        href="{{ route('proprietaire.tickets.index', array_merge($autresFiltres, ['statut' => $opt->value])) }}"
                        @class([
                            'rounded-full px-3 py-1.5 text-xs font-semibold transition',
                            'bg-gray-900 text-white' => $filtres['statut'] === $opt->value,
                            'bg-gray-100 text-gray-600 hover:bg-gray-200' => $filtres['statut'] !== $opt->value,
                        ])
                    >{{ $opt->label() }}</a>
                @endforeach
            </div>

            {{-- Filtres complémentaires --}}
            <section class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                <form
                    method="GET"
                    action="{{ route('proprietaire.tickets.index') }}"
                    x-data
                    @change="$root.submit()"
                    @input.debounce.500ms="$root.submit()"
                >
                    @if ($filtres['statut'])
                        <input type="hidden" name="statut" value="{{ $filtres['statut'] }}">
                    @endif
                    <div class="flex flex-wrap items-end gap-3">
                        <div class="flex-1 min-w-48">
                            <x-text-input
                                name="recherche"
                                type="text"
                                class="block w-full"
                                :value="$filtres['recherche']"
                                placeholder="{{ __('Titre, locataire, bien…') }}"
                            />
                        </div>

                        <select name="priorite" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2">
                            <option value="">{{ __('Toutes les priorités') }}</option>
                            @foreach ($prioriteOptions as $opt)
                                <option value="{{ $opt->value }}" @selected($filtres['priorite'] === $opt->value)>{{ $opt->label() }}</option>
                            @endforeach
                        </select>

                        <select name="categorie" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2">
                            <option value="">{{ __('Toutes les catégories') }}</option>
                            @foreach ($categorieOptions as $opt)
                                <option value="{{ $opt->value }}" @selected($filtres['categorie'] === $opt->value)>{{ $opt->label() }}</option>
                            @endforeach
                        </select>

                        <select name="bien_id" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2">
                            <option value="">{{ __('Tous les biens') }}</option>
                            @foreach ($biens as $bien)
                                <option value="{{ $bien->id }}" @selected($filtres['bienId'] === (string) $bien->id)>{{ $bien->nom }}</option>
                            @endforeach
                        </select>

                        <select name="locataire_id" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2">
                            <option value="">{{ __('Tous les locataires') }}</option>
                            @foreach ($locataires as $locataire)
                                <option value="{{ $locataire->id }}" @selected($filtres['locataireId'] === (string) $locataire->id)>{{ $locataire->nomComplet() }}</option>
                            @endforeach
                        </select>

                        <button type="submit" class="hidden" tabindex="-1" aria-hidden="true">{{ __('Filtrer') }}</button>

                        @if (array_filter([$filtres['recherche'], $filtres['statut'], $filtres['priorite'], $filtres['categorie'], $filtres['bienId'], $filtres['locataireId']]))
                            <a href="{{ route('proprietaire.tickets.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
                                {{ __('Réinitialiser') }}
                            </a>
                        @endif
                    </div>
                </form>
            </section>

            @if ($tickets->isEmpty())
                <div class="rounded-2xl border border-dashed border-gray-200 bg-white p-12 text-center shadow-sm">
                    <p class="text-3xl text-gray-200">🔧</p>
                    <h3 class="mt-4 text-lg font-semibold text-gray-900">{{ __('Aucun ticket') }}</h3>
                    <p class="mt-2 text-sm text-gray-500">{{ __('Ajustez vos filtres ou attendez la prochaine demande d\'un locataire.') }}</p>
                </div>
            @else
                {{-- Table --}}
                <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                    <table class="w-full text-sm">
                        <thead class="border-b border-gray-100 bg-slate-50">
                            <tr>
                                <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Ticket') }}</th>
                                <th class="px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Locataire') }}</th>
                                <th class="hidden px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-400 lg:table-cell">{{ __('Bien') }}</th>
                                <th class="px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Statut') }}</th>
                                <th class="hidden px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-400 sm:table-cell">{{ __('Activité') }}</th>
                                <th class="px-6 py-3.5"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($tickets as $ticket)
                                <tr
                                    onclick="window.location='{{ route('proprietaire.tickets.show', $ticket) }}'"
                                    class="group cursor-pointer transition-colors hover:bg-slate-50"
                                >
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <span @class([
                                                'shrink-0 h-2 w-2 rounded-full',
                                                'bg-red-400' => $ticket->priorite->value === 'haute',
                                                'bg-amber-400' => $ticket->priorite->value === 'moyenne',
                                                'bg-gray-300' => $ticket->priorite->value === 'basse',
                                            ])></span>
                                            <div class="min-w-0">
                                                <p class="truncate font-medium text-gray-900">{{ $ticket->titre }}</p>
                                                <p class="mt-0.5 text-xs text-gray-400">{{ $ticket->categorie->label() }}
                                                    @if ($ticket->messages_count > 0)
                                                        · {{ $ticket->messages_count }} msg
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-gray-700">{{ $ticket->contrat->locataire->nomComplet() }}</td>
                                    <td class="hidden px-4 py-4 text-gray-500 lg:table-cell">{{ $ticket->contrat->bien->nom }}</td>
                                    <td class="px-4 py-4"><x-tickets.status-badge :status="$ticket->statut" /></td>
                                    <td class="hidden px-4 py-4 text-xs text-gray-400 sm:table-cell">{{ $ticket->updated_at->diffForHumans() }}</td>
                                    <td class="px-6 py-4 text-right">
                                        <svg class="ml-auto h-4 w-4 text-gray-300 transition group-hover:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </section>

                <div>{{ $tickets->links() }}</div>
            @endif

        </div>
    </div>
</x-app-layout>
