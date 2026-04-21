<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    {{ __('Tickets de maintenance') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Signalez un incident, suivez son statut et échangez avec votre propriétaire.') }}
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto grid max-w-7xl gap-6 px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            <div class="grid gap-6 xl:grid-cols-[minmax(0,1.15fr)_minmax(320px,0.85fr)]">
                <section class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="flex flex-col gap-2">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('Nouveau ticket') }}</h3>
                        <p class="text-sm text-gray-500">
                            {{ __('Décrivez clairement le problème rencontré et ajoutez des photos si nécessaire.') }}
                        </p>
                    </div>

                    @if (! $contratActif)
                        <div class="mt-6 rounded-2xl border border-dashed border-gray-300 bg-gray-50 px-5 py-6 text-sm text-gray-600">
                            {{ __('Vous devez disposer d\'un contrat actif pour créer un ticket de maintenance.') }}
                        </div>
                    @else
                        <form
                            method="POST"
                            action="{{ route('locataire.tickets.store') }}"
                            enctype="multipart/form-data"
                            class="mt-6 grid gap-5"
                        >
                            @csrf

                            <div>
                                <x-input-label for="titre" :value="__('Titre')" />
                                <x-text-input id="titre" name="titre" type="text" class="mt-1 block w-full" :value="old('titre')" required />
                                <x-input-error :messages="$errors->get('titre')" class="mt-2" />
                            </div>

                            <div class="grid gap-5 md:grid-cols-2">
                                <div>
                                    <x-input-label for="categorie" :value="__('Catégorie')" />
                                    <select id="categorie" name="categorie" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        @foreach ($categorieOptions as $categorieOption)
                                            <option value="{{ $categorieOption->value }}" @selected(old('categorie', $categorieOptions[0]->value) === $categorieOption->value)>
                                                {{ $categorieOption->label() }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('categorie')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="priorite" :value="__('Priorité')" />
                                    <select id="priorite" name="priorite" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        @foreach ($prioriteOptions as $prioriteOption)
                                            <option value="{{ $prioriteOption->value }}" @selected(old('priorite', $prioriteOptions[1]->value ?? $prioriteOptions[0]->value) === $prioriteOption->value)>
                                                {{ $prioriteOption->label() }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('priorite')" class="mt-2" />
                                </div>
                            </div>

                            <div>
                                <x-input-label for="description" :value="__('Description')" />
                                <textarea
                                    id="description"
                                    name="description"
                                    rows="5"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required
                                >{{ old('description') }}</textarea>
                                <x-input-error :messages="$errors->get('description')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="photos" :value="__('Photos (optionnel)')" />
                                <input
                                    id="photos"
                                    name="photos[]"
                                    type="file"
                                    accept="image/*"
                                    multiple
                                    class="mt-1 block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 shadow-sm file:me-4 file:rounded-md file:border-0 file:bg-gray-900 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-gray-700"
                                />
                                <p class="mt-2 text-xs text-gray-500">{{ __('Jusqu\'à 5 images, 5 Mo maximum par fichier.') }}</p>
                                <x-input-error :messages="$errors->get('photos')" class="mt-2" />
                                <x-input-error :messages="$errors->get('photos.*')" class="mt-2" />
                            </div>

                            <x-primary-button class="justify-center">
                                {{ __('Créer le ticket') }}
                            </x-primary-button>
                        </form>
                    @endif
                </section>

                <aside class="grid gap-6">
                    <section class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('Contrat utilisé') }}</h3>

                        @if ($contratActif)
                            <dl class="mt-5 grid gap-4 text-sm text-gray-700">
                                <div>
                                    <dt class="text-xs uppercase tracking-wide text-gray-500">{{ __('Bien') }}</dt>
                                    <dd class="mt-2 font-semibold text-gray-900">{{ $contratActif->bien->nom }}</dd>
                                </div>

                                <div>
                                    <dt class="text-xs uppercase tracking-wide text-gray-500">{{ __('Adresse') }}</dt>
                                    <dd class="mt-2">{{ $contratActif->bien->adresse }}, {{ $contratActif->bien->ville }}</dd>
                                </div>

                                <div>
                                    <dt class="text-xs uppercase tracking-wide text-gray-500">{{ __('Tickets actifs') }}</dt>
                                    <dd class="mt-2 font-semibold text-gray-900">{{ $ticketsActifsContratCount }}</dd>
                                </div>
                            </dl>
                        @else
                            <p class="mt-4 text-sm text-gray-500">{{ __('Aucun contrat actif disponible pour l\'instant.') }}</p>
                        @endif
                    </section>

                    <section class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('Filtrer mes tickets') }}</h3>

                        <form method="GET" action="{{ route('locataire.tickets.index') }}" class="mt-5 grid gap-4">
                            <div>
                                <x-input-label for="recherche" :value="__('Recherche')" />
                                <x-text-input id="recherche" name="recherche" type="text" class="mt-1 block w-full" :value="$filtres['recherche']" />
                            </div>

                            <div>
                                <x-input-label for="statut" :value="__('Statut')" />
                                <select id="statut" name="statut" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">{{ __('Tous les statuts') }}</option>
                                    @foreach ($statutOptions as $statutOption)
                                        <option value="{{ $statutOption->value }}" @selected($filtres['statut'] === $statutOption->value)>
                                            {{ $statutOption->label() }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="flex flex-wrap gap-3">
                                <x-primary-button>{{ __('Filtrer') }}</x-primary-button>
                                <a
                                    href="{{ route('locataire.tickets.index') }}"
                                    class="inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition hover:bg-gray-50"
                                >
                                    {{ __('Réinitialiser') }}
                                </a>
                            </div>
                        </form>
                    </section>
                </aside>
            </div>

            <section class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('Mes tickets') }}</h3>
                        <p class="mt-1 text-sm text-gray-500">{{ __('Consultez vos demandes et suivez leur traitement.') }}</p>
                    </div>
                </div>

                @if ($tickets->isEmpty())
                    <div class="mt-6 rounded-2xl border border-dashed border-gray-300 px-6 py-10 text-center">
                        <p class="text-sm text-gray-500">{{ __('Aucun ticket ne correspond à votre recherche pour le moment.') }}</p>
                    </div>
                @else
                    <div class="mt-6 grid gap-4">
                        @foreach ($tickets as $ticket)
                            <article class="grid gap-4 rounded-2xl border border-gray-200 p-5 md:grid-cols-[minmax(0,1fr)_auto] md:items-center">
                                <div class="grid gap-3">
                                    <div class="flex flex-wrap items-center gap-3">
                                        <p class="text-lg font-semibold text-gray-900">{{ $ticket->titre }}</p>
                                        <x-tickets.status-badge :status="$ticket->statut" />
                                        <x-tickets.priority-badge :priority="$ticket->priorite" />
                                    </div>

                                    <div class="grid gap-1 text-sm text-gray-600">
                                        <p>{{ __('Bien : :bien', ['bien' => $ticket->contrat->bien->nom]) }}</p>
                                        <p>{{ __('Catégorie : :categorie', ['categorie' => $ticket->categorie->label()]) }}</p>
                                        <p>{{ __('Messages : :count', ['count' => $ticket->messages_count]) }}</p>
                                    </div>
                                </div>

                                <a
                                    href="{{ route('locataire.tickets.show', $ticket) }}"
                                    class="inline-flex items-center justify-center rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50"
                                >
                                    {{ __('Voir le ticket') }}
                                </a>
                            </article>
                        @endforeach
                    </div>

                    <div class="mt-6">
                        {{ $tickets->links() }}
                    </div>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>
