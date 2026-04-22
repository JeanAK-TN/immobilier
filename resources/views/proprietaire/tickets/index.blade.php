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

            {{-- Filtres compacts --}}
            <section class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                <form method="GET" action="{{ route('proprietaire.tickets.index') }}">
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

                        <select name="statut" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2">
                            <option value="">{{ __('Tous les statuts') }}</option>
                            @foreach ($statutOptions as $opt)
                                <option value="{{ $opt->value }}" @selected($filtres['statut'] === $opt->value)>{{ $opt->label() }}</option>
                            @endforeach
                        </select>

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

                        <x-primary-button>{{ __('Filtrer') }}</x-primary-button>

                        @if (array_filter([$filtres['recherche'], $filtres['statut'], $filtres['priorite'], $filtres['categorie'], $filtres['bienId'], $filtres['locataireId']]))
                            <a href="{{ route('proprietaire.tickets.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
                                {{ __('Réinitialiser') }}
                            </a>
                        @endif
                    </div>
                </form>
            </section>

            @if ($tickets->isEmpty())
                <section class="rounded-2xl border border-dashed border-gray-300 bg-white p-12 text-center shadow-sm">
                    <p class="text-4xl text-gray-200">🔧</p>
                    <h3 class="mt-4 text-lg font-semibold text-gray-900">{{ __('Aucun ticket trouvé') }}</h3>
                    <p class="mt-2 text-sm text-gray-500">{{ __('Ajustez vos filtres ou attendez la prochaine demande d\'un locataire.') }}</p>
                </section>
            @else
                {{-- Table --}}
                <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                    <table class="w-full text-sm">
                        <thead class="border-b border-gray-100 bg-slate-50">
                            <tr>
                                <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Ticket') }}</th>
                                <th class="px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Locataire') }}</th>
                                <th class="hidden px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-400 lg:table-cell">{{ __('Bien') }}</th>
                                <th class="px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Priorité') }}</th>
                                <th class="px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Statut') }}</th>
                                <th class="hidden px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-400 sm:table-cell">{{ __('Activité') }}</th>
                                <th class="px-6 py-3.5"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($tickets as $ticket)
                                <tr @class([
                                    'group transition-colors hover:bg-slate-50',
                                    'bg-red-50/50' => $ticket->priorite->value === 'haute',
                                ])>
                                    <td class="px-6 py-4">
                                        <p class="font-medium text-gray-900">{{ $ticket->titre }}</p>
                                        <p class="mt-0.5 text-xs text-gray-400">{{ $ticket->categorie->label() }}
                                            @if ($ticket->messages_count > 0)
                                                · {{ $ticket->messages_count }} msg
                                            @endif
                                        </p>
                                    </td>
                                    <td class="px-4 py-4 text-gray-700">{{ $ticket->contrat->locataire->nomComplet() }}</td>
                                    <td class="hidden px-4 py-4 text-gray-500 lg:table-cell">{{ $ticket->contrat->bien->nom }}</td>
                                    <td class="px-4 py-4"><x-tickets.priority-badge :priority="$ticket->priorite" /></td>
                                    <td class="px-4 py-4"><x-tickets.status-badge :status="$ticket->statut" /></td>
                                    <td class="hidden px-4 py-4 text-xs text-gray-400 sm:table-cell">{{ $ticket->updated_at->diffForHumans() }}</td>
                                    <td class="px-6 py-4 text-right">
                                        <a
                                            href="{{ route('proprietaire.tickets.show', $ticket) }}"
                                            class="inline-flex items-center gap-1 text-sm font-semibold text-gray-700 transition hover:text-gray-900"
                                        >
                                            {{ __('Traiter') }}
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                                        </a>
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
