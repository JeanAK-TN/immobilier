<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-900">
                    {{ __('Tickets de maintenance') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Suivez les incidents remontés par vos locataires, traitez-les et échangez depuis un seul écran.') }}
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto grid max-w-7xl gap-6 px-4 sm:px-6 lg:px-8">

            {{-- Filtres --}}
            <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <form method="GET" action="{{ route('proprietaire.tickets.index') }}" class="space-y-4">
                    <div>
                        <x-input-label for="recherche" :value="__('Recherche')" />
                        <x-text-input
                            id="recherche"
                            name="recherche"
                            type="text"
                            class="mt-1 block w-full"
                            :value="$filtres['recherche']"
                            placeholder="{{ __('Titre, description, locataire...') }}"
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
                        <div>
                            <x-input-label for="statut" :value="__('Statut')" />
                            <select id="statut" name="statut" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">{{ __('Tous') }}</option>
                                @foreach ($statutOptions as $statutOption)
                                    <option value="{{ $statutOption->value }}" @selected($filtres['statut'] === $statutOption->value)>{{ $statutOption->label() }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <x-input-label for="priorite" :value="__('Priorité')" />
                            <select id="priorite" name="priorite" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">{{ __('Toutes') }}</option>
                                @foreach ($prioriteOptions as $prioriteOption)
                                    <option value="{{ $prioriteOption->value }}" @selected($filtres['priorite'] === $prioriteOption->value)>{{ $prioriteOption->label() }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <x-input-label for="categorie" :value="__('Catégorie')" />
                            <select id="categorie" name="categorie" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">{{ __('Toutes') }}</option>
                                @foreach ($categorieOptions as $categorieOption)
                                    <option value="{{ $categorieOption->value }}" @selected($filtres['categorie'] === $categorieOption->value)>{{ $categorieOption->label() }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <x-input-label for="bien_id" :value="__('Bien')" />
                            <select id="bien_id" name="bien_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">{{ __('Tous') }}</option>
                                @foreach ($biens as $bien)
                                    <option value="{{ $bien->id }}" @selected($filtres['bienId'] === (string) $bien->id)>{{ $bien->nom }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <x-input-label for="locataire_id" :value="__('Locataire')" />
                            <select id="locataire_id" name="locataire_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">{{ __('Tous') }}</option>
                                @foreach ($locataires as $locataire)
                                    <option value="{{ $locataire->id }}" @selected($filtres['locataireId'] === (string) $locataire->id)>{{ $locataire->nomComplet() }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-3 border-t border-gray-100 pt-4">
                        <x-primary-button>{{ __('Filtrer') }}</x-primary-button>
                        <a
                            href="{{ route('proprietaire.tickets.index') }}"
                            class="inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition hover:bg-gray-50"
                        >
                            {{ __('Réinitialiser') }}
                        </a>
                    </div>
                </form>
            </section>

            @if ($tickets->isEmpty())
                <section class="rounded-2xl border border-dashed border-gray-300 bg-white p-12 text-center shadow-sm">
                    <p class="text-4xl text-gray-300">🔧</p>
                    <h3 class="mt-4 text-lg font-semibold text-gray-900">{{ __('Aucun ticket trouvé') }}</h3>
                    <p class="mt-2 text-sm text-gray-500">{{ __('Ajustez vos filtres ou attendez la prochaine demande d\'un locataire.') }}</p>
                </section>
            @else
                <section class="grid gap-5 lg:grid-cols-2 xl:grid-cols-3">
                    @foreach ($tickets as $ticket)
                        <article class="flex flex-col gap-4 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition hover:shadow-md">
                            <div class="flex items-start justify-between gap-4">
                                <div class="min-w-0">
                                    <p class="truncate font-semibold text-gray-900">{{ $ticket->titre }}</p>
                                    <p class="mt-0.5 text-sm text-gray-500">{{ $ticket->contrat->locataire->nomComplet() }}</p>
                                    <p class="text-sm text-gray-400">{{ $ticket->contrat->bien->nom }}</p>
                                </div>
                                <x-tickets.status-badge :status="$ticket->statut" />
                            </div>

                            <div class="flex flex-wrap gap-2">
                                <x-tickets.priority-badge :priority="$ticket->priorite" />
                                <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600">
                                    {{ $ticket->categorie->label() }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between border-t border-gray-100 pt-4">
                                <div class="text-xs text-gray-400">
                                    <span>{{ $ticket->messages_count }} msg</span>
                                    <span class="mx-1">·</span>
                                    <span>{{ $ticket->created_at->translatedFormat('d M Y') }}</span>
                                </div>
                                <a
                                    href="{{ route('proprietaire.tickets.show', $ticket) }}"
                                    class="inline-flex items-center rounded-md bg-gray-900 px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition hover:bg-gray-700"
                                >
                                    {{ __('Traiter') }}
                                </a>
                            </div>
                        </article>
                    @endforeach
                </section>

                <div>
                    {{ $tickets->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
