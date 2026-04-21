<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    {{ __('Tickets de maintenance') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Suivez les incidents remontés par vos locataires, traitez-les et échangez depuis un seul écran.') }}
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto grid max-w-7xl gap-6 px-4 sm:px-6 lg:px-8">
            <section class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                <form method="GET" action="{{ route('proprietaire.tickets.index') }}" class="grid gap-4 lg:grid-cols-3 xl:grid-cols-6 xl:items-end">
                    <div class="grid gap-2 xl:col-span-2">
                        <x-input-label for="recherche" :value="__('Recherche')" />
                        <x-text-input id="recherche" name="recherche" type="text" class="w-full" :value="$filtres['recherche']" />
                    </div>

                    <div class="grid gap-2">
                        <x-input-label for="statut" :value="__('Statut')" />
                        <select id="statut" name="statut" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">{{ __('Tous les statuts') }}</option>
                            @foreach ($statutOptions as $statutOption)
                                <option value="{{ $statutOption->value }}" @selected($filtres['statut'] === $statutOption->value)>{{ $statutOption->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid gap-2">
                        <x-input-label for="priorite" :value="__('Priorité')" />
                        <select id="priorite" name="priorite" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">{{ __('Toutes les priorités') }}</option>
                            @foreach ($prioriteOptions as $prioriteOption)
                                <option value="{{ $prioriteOption->value }}" @selected($filtres['priorite'] === $prioriteOption->value)>{{ $prioriteOption->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid gap-2">
                        <x-input-label for="categorie" :value="__('Catégorie')" />
                        <select id="categorie" name="categorie" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">{{ __('Toutes les catégories') }}</option>
                            @foreach ($categorieOptions as $categorieOption)
                                <option value="{{ $categorieOption->value }}" @selected($filtres['categorie'] === $categorieOption->value)>{{ $categorieOption->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid gap-2">
                        <x-input-label for="bien_id" :value="__('Bien')" />
                        <select id="bien_id" name="bien_id" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">{{ __('Tous les biens') }}</option>
                            @foreach ($biens as $bien)
                                <option value="{{ $bien->id }}" @selected($filtres['bienId'] === (string) $bien->id)>{{ $bien->nom }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid gap-2">
                        <x-input-label for="locataire_id" :value="__('Locataire')" />
                        <select id="locataire_id" name="locataire_id" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">{{ __('Tous les locataires') }}</option>
                            @foreach ($locataires as $locataire)
                                <option value="{{ $locataire->id }}" @selected($filtres['locataireId'] === (string) $locataire->id)>{{ $locataire->nomComplet() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex flex-wrap items-center gap-3 xl:col-span-6">
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
                <section class="rounded-3xl border border-dashed border-gray-300 bg-white p-10 text-center shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('Aucun ticket trouvé') }}</h3>
                    <p class="mt-2 text-sm text-gray-500">{{ __('Ajustez vos filtres ou attendez la prochaine demande d\'un locataire.') }}</p>
                </section>
            @else
                <section class="grid gap-5 lg:grid-cols-2 xl:grid-cols-3">
                    @foreach ($tickets as $ticket)
                        <article class="grid gap-4 rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-lg font-semibold text-gray-900">{{ $ticket->titre }}</p>
                                    <p class="mt-1 text-sm text-gray-500">{{ $ticket->contrat->locataire->nomComplet() }}</p>
                                    <p class="mt-1 text-sm text-gray-500">{{ $ticket->contrat->bien->nom }}</p>
                                </div>

                                <x-tickets.status-badge :status="$ticket->statut" />
                            </div>

                            <div class="flex flex-wrap gap-2">
                                <x-tickets.priority-badge :priority="$ticket->priorite" />
                                <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600">
                                    {{ $ticket->categorie->label() }}
                                </span>
                            </div>

                            <div class="grid gap-2 text-sm text-gray-600">
                                <p>{{ __('Messages : :count', ['count' => $ticket->messages_count]) }}</p>
                                <p>{{ __('Créé le :date', ['date' => $ticket->created_at->translatedFormat('d M Y')]) }}</p>
                            </div>

                            <a
                                href="{{ route('proprietaire.tickets.show', $ticket) }}"
                                class="inline-flex items-center justify-center rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50"
                            >
                                {{ __('Traiter le ticket') }}
                            </a>
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
