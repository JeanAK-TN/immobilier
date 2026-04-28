<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-semibold leading-tight text-gray-900">{{ __('Tickets de maintenance') }}</h2>
            <p class="mt-1 text-sm text-gray-500">{{ __('Signalez un incident, suivez son statut et échangez avec votre propriétaire.') }}</p>
        </div>
    </x-slot>

    <div
        class="py-8"
        x-data="{ createOpen: {{ ($ticketsTotalContratCount === 0 && $contratActif) ? 'true' : 'false' }} }"
    >
        <div class="mx-auto max-w-4xl space-y-5 px-4 sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3.5 text-sm font-medium text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Contrat actif + bouton Nouveau ticket --}}
            @if ($contratActif)
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <div class="flex items-center gap-3 rounded-xl border border-gray-200 bg-white px-4 py-3 shadow-sm sm:flex-1">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-blue-50">
                            <svg class="h-4 w-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-gray-900">{{ $contratActif->bien->nom }}</p>
                            <p class="truncate text-xs text-gray-400">{{ $contratActif->bien->adresse }}, {{ $contratActif->bien->ville }}</p>
                        </div>
                        @if ($ticketsActifsContratCount > 0)
                            <span class="shrink-0 rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-700">
                                {{ $ticketsActifsContratCount }} actif{{ $ticketsActifsContratCount > 1 ? 's' : '' }}
                            </span>
                        @else
                            <span class="shrink-0 rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-700">
                                {{ __('Aucun incident actif') }}
                            </span>
                        @endif
                    </div>

                    <button
                        type="button"
                        x-on:click="createOpen = !createOpen"
                        class="inline-flex shrink-0 items-center justify-center gap-2 rounded-lg bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-700"
                    >
                        <span x-text="createOpen ? '✕ Annuler' : '+ Nouveau ticket'"></span>
                    </button>
                </div>
            @endif

            {{-- Formulaire de création (collapsible) --}}
            @if ($contratActif)
                <div x-show="createOpen" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                    <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                        <div class="mb-5">
                            <h3 class="text-lg font-semibold text-gray-900">{{ __('Nouveau ticket') }}</h3>
                            <p class="mt-1 text-sm text-gray-500">{{ __('Décrivez clairement le problème rencontré et ajoutez des photos si nécessaire.') }}</p>
                        </div>

                        <form
                            method="POST"
                            action="{{ route('locataire.tickets.store') }}"
                            enctype="multipart/form-data"
                            class="grid gap-5 sm:grid-cols-2"
                        >
                            @csrf

                            <div class="sm:col-span-2">
                                <x-input-label for="titre" :value="__('Titre')" />
                                <x-text-input id="titre" name="titre" type="text" class="mt-1 block w-full" :value="old('titre')" required />
                                <x-input-error :messages="$errors->get('titre')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="categorie" :value="__('Catégorie')" />
                                <select id="categorie" name="categorie" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500">
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
                                <select id="priorite" name="priorite" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500">
                                    @foreach ($prioriteOptions as $prioriteOption)
                                        <option value="{{ $prioriteOption->value }}" @selected(old('priorite', $prioriteOptions[1]->value ?? $prioriteOptions[0]->value) === $prioriteOption->value)>
                                            {{ $prioriteOption->label() }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('priorite')" class="mt-2" />
                            </div>

                            <div class="sm:col-span-2">
                                <x-input-label for="description" :value="__('Description')" />
                                <textarea
                                    id="description"
                                    name="description"
                                    rows="4"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500"
                                    required
                                >{{ old('description') }}</textarea>
                                <x-input-error :messages="$errors->get('description')" class="mt-2" />
                            </div>

                            <div class="sm:col-span-2">
                                <x-input-label for="photos" :value="__('Photos (optionnel)')" />
                                <input
                                    id="photos"
                                    name="photos[]"
                                    type="file"
                                    accept="image/*"
                                    multiple
                                    class="mt-1 block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 shadow-sm file:me-4 file:rounded-md file:border-0 file:bg-gray-900 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-gray-700"
                                />
                                <p class="mt-1.5 text-xs text-gray-400">{{ __('Jusqu\'à 5 images, 5 Mo maximum par fichier.') }}</p>
                                <x-input-error :messages="$errors->get('photos')" class="mt-2" />
                                <x-input-error :messages="$errors->get('photos.*')" class="mt-2" />
                            </div>

                            <div class="sm:col-span-2 flex flex-wrap items-center gap-3 border-t border-gray-100 pt-4">
                                <x-primary-button>{{ __('Créer le ticket') }}</x-primary-button>
                                <button type="button" x-on:click="createOpen = false" class="text-sm text-gray-500 hover:text-gray-700">
                                    {{ __('Annuler') }}
                                </button>
                            </div>
                        </form>
                    </section>
                </div>
            @endif

            {{-- Filtres : onglets statut + recherche --}}
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                {{-- Onglets statut --}}
                <div class="flex flex-wrap gap-1.5">
                    <a
                        href="{{ route('locataire.tickets.index', array_filter(['recherche' => $filtres['recherche']])) }}"
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
                            href="{{ route('locataire.tickets.index', array_filter(['statut' => $opt->value, 'recherche' => $filtres['recherche']])) }}"
                            @class([
                                'rounded-full px-3 py-1.5 text-xs font-semibold transition',
                                'bg-gray-900 text-white' => $filtres['statut'] === $opt->value,
                                'bg-gray-100 text-gray-600 hover:bg-gray-200' => $filtres['statut'] !== $opt->value,
                            ])
                        >{{ $opt->label() }}</a>
                    @endforeach
                </div>

                {{-- Recherche --}}
                <form
                    method="GET"
                    action="{{ route('locataire.tickets.index') }}"
                    x-data
                    @input.debounce.500ms="$root.submit()"
                    class="flex items-center gap-2"
                >
                    @if ($filtres['statut'])
                        <input type="hidden" name="statut" value="{{ $filtres['statut'] }}">
                    @endif
                    <x-text-input
                        name="recherche"
                        type="text"
                        class="block w-44 text-sm"
                        :value="$filtres['recherche']"
                        placeholder="{{ __('Rechercher…') }}"
                    />
                    <button type="submit" class="hidden" tabindex="-1" aria-hidden="true">{{ __('OK') }}</button>
                    @if ($filtres['recherche'])
                        <a href="{{ route('locataire.tickets.index', array_filter(['statut' => $filtres['statut']])) }}" class="text-xs text-gray-400 hover:text-gray-600">✕</a>
                    @endif
                </form>
            </div>

            {{-- Liste --}}
            @if (! $contratActif && $tickets->isEmpty())
                <section class="rounded-2xl border border-dashed border-gray-300 bg-white p-12 text-center shadow-sm">
                    <p class="text-4xl text-gray-300">🔧</p>
                    <h3 class="mt-4 text-lg font-semibold text-gray-900">{{ __('Aucun contrat actif') }}</h3>
                    <p class="mt-2 text-sm text-gray-500">{{ __('Vous devez disposer d\'un contrat actif pour créer un ticket de maintenance.') }}</p>
                </section>
            @elseif ($tickets->isEmpty())
                <section class="rounded-2xl border border-dashed border-gray-300 bg-white p-12 text-center shadow-sm">
                    <p class="text-4xl text-gray-300">✅</p>
                    <h3 class="mt-4 text-lg font-semibold text-gray-900">{{ __('Aucun ticket trouvé') }}</h3>
                    <p class="mt-2 text-sm text-gray-500">{{ __('Aucun ticket ne correspond à votre recherche.') }}</p>
                </section>
            @else
                <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm divide-y divide-gray-100">
                    @foreach ($tickets as $ticket)
                        <a
                            href="{{ route('locataire.tickets.show', $ticket) }}"
                            class="group flex items-center gap-4 px-5 py-4 transition hover:bg-slate-50"
                        >
                            {{-- Indicateur priorité --}}
                            <span @class([
                                'shrink-0 h-2 w-2 rounded-full',
                                'bg-red-400' => $ticket->priorite->value === 'haute',
                                'bg-amber-400' => $ticket->priorite->value === 'moyenne',
                                'bg-gray-300' => $ticket->priorite->value === 'basse',
                            ])></span>

                            {{-- Titre + méta --}}
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-gray-900 group-hover:text-gray-700">{{ $ticket->titre }}</p>
                                <p class="mt-0.5 text-xs text-gray-400">
                                    {{ $ticket->categorie->label() }}
                                    @if ($ticket->messages_count > 0)
                                        · {{ $ticket->messages_count }} msg
                                    @endif
                                    · {{ $ticket->updated_at->diffForHumans() }}
                                </p>
                            </div>

                            {{-- Statut --}}
                            <div class="shrink-0 hidden sm:block">
                                <x-tickets.status-badge :status="$ticket->statut" />
                            </div>

                            {{-- Flèche --}}
                            <svg class="h-4 w-4 shrink-0 text-gray-300 group-hover:text-gray-500 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    @endforeach
                </div>

                <div>{{ $tickets->links() }}</div>
            @endif

        </div>
    </div>
</x-app-layout>
