<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-900">
                    {{ __('Tickets de maintenance') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Signalez un incident, suivez son statut et échangez avec votre propriétaire.') }}
                </p>
            </div>
        </div>
    </x-slot>

    <div
        class="py-8"
        x-data="{ createOpen: {{ ($tickets->isEmpty() && $contratActif) ? 'true' : 'false' }} }"
    >
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3.5 text-sm font-medium text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Barre d'actions : titre de la liste + bouton toggle --}}
            <div class="flex items-center justify-between gap-4">
                <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider">
                    {{ __('Mes tickets') }}
                    @if ($tickets->total() > 0)
                        <span class="ml-2 rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-600">{{ $tickets->total() }}</span>
                    @endif
                </p>

                @if ($contratActif)
                    <button
                        type="button"
                        @click="createOpen = !createOpen"
                        class="inline-flex items-center gap-2 rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-700"
                    >
                        <span x-text="createOpen ? '✕ Annuler' : '+ Nouveau ticket'"></span>
                    </button>
                @endif
            </div>

            {{-- Formulaire de création (collapsible) --}}
            @if ($contratActif)
                <div x-show="createOpen" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                    <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                        <div class="mb-5 flex items-center justify-between gap-3">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ __('Nouveau ticket') }}</h3>
                                <p class="mt-1 text-sm text-gray-500">{{ __('Décrivez clairement le problème rencontré et ajoutez des photos si nécessaire.') }}</p>
                            </div>
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

                            <div class="sm:col-span-2">
                                <x-input-label for="description" :value="__('Description')" />
                                <textarea
                                    id="description"
                                    name="description"
                                    rows="4"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
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
                                <button type="button" @click="createOpen = false" class="text-sm text-gray-500 hover:text-gray-700">
                                    {{ __('Annuler') }}
                                </button>
                            </div>
                        </form>
                    </section>
                </div>
            @endif

            {{-- Contenu principal : filtres + liste --}}
            <div class="grid gap-6 xl:grid-cols-[minmax(240px,0.28fr)_minmax(0,1fr)]">

                {{-- Sidebar : contrat + filtres --}}
                <aside class="grid gap-4 content-start">
                    @if ($contratActif)
                        <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Contrat actif') }}</p>
                            <p class="mt-2 font-semibold text-gray-900">{{ $contratActif->bien->nom }}</p>
                            <p class="mt-0.5 text-xs text-gray-500">{{ $contratActif->bien->adresse }}, {{ $contratActif->bien->ville }}</p>
                            <div class="mt-3 flex items-center gap-2">
                                <span class="inline-block h-2 w-2 rounded-full bg-emerald-400"></span>
                                <span class="text-xs text-gray-500">{{ $ticketsActifsContratCount }} {{ __('ticket(s) actif(s)') }}</span>
                            </div>
                        </section>
                    @endif

                    <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Filtres') }}</p>

                        <form method="GET" action="{{ route('locataire.tickets.index') }}" class="mt-4 grid gap-3">
                            <div>
                                <x-input-label for="recherche" :value="__('Recherche')" />
                                <x-text-input id="recherche" name="recherche" type="text" class="mt-1 block w-full" :value="$filtres['recherche']" placeholder="{{ __('Titre...') }}" />
                            </div>

                            <div>
                                <x-input-label for="statut" :value="__('Statut')" />
                                <select id="statut" name="statut" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">{{ __('Tous') }}</option>
                                    @foreach ($statutOptions as $statutOption)
                                        <option value="{{ $statutOption->value }}" @selected($filtres['statut'] === $statutOption->value)>
                                            {{ $statutOption->label() }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="flex flex-wrap gap-2 pt-1">
                                <x-primary-button class="flex-1 justify-center">{{ __('Filtrer') }}</x-primary-button>
                                <a
                                    href="{{ route('locataire.tickets.index') }}"
                                    class="inline-flex flex-1 items-center justify-center rounded-md border border-gray-300 px-3 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition hover:bg-gray-50"
                                >
                                    {{ __('Réinit.') }}
                                </a>
                            </div>
                        </form>
                    </section>
                </aside>

                {{-- Liste des tickets --}}
                <section class="grid gap-4 content-start">
                    @if (! $contratActif && $tickets->isEmpty())
                        <div class="rounded-2xl border border-dashed border-gray-200 bg-white p-12 text-center shadow-sm">
                            <p class="text-3xl text-gray-200">🔧</p>
                            <h3 class="mt-4 text-lg font-semibold text-gray-900">{{ __('Aucun contrat actif') }}</h3>
                            <p class="mt-2 text-sm text-gray-500">{{ __('Vous devez disposer d\'un contrat actif pour créer un ticket de maintenance.') }}</p>
                        </div>
                    @elseif ($tickets->isEmpty())
                        <div class="rounded-2xl border border-dashed border-gray-200 bg-white p-12 text-center shadow-sm">
                            <p class="text-3xl text-gray-200">✅</p>
                            <h3 class="mt-4 text-lg font-semibold text-gray-900">{{ __('Aucun ticket') }}</h3>
                            <p class="mt-2 text-sm text-gray-500">{{ __('Aucun ticket ne correspond à votre recherche.') }}</p>
                        </div>
                    @else
                        @foreach ($tickets as $ticket)
                            <a
                                href="{{ route('locataire.tickets.show', $ticket) }}"
                                class="group flex flex-col gap-3 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition hover:shadow-md hover:border-gray-300"
                            >
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="truncate font-semibold text-gray-900 group-hover:text-gray-700">{{ $ticket->titre }}</p>
                                        <p class="mt-0.5 text-xs text-gray-400">{{ $ticket->contrat->bien->nom }}</p>
                                    </div>
                                    <x-tickets.status-badge :status="$ticket->statut" />
                                </div>

                                <div class="flex flex-wrap items-center gap-2">
                                    <x-tickets.priority-badge :priority="$ticket->priorite" />
                                    <span class="rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-500">{{ $ticket->categorie->label() }}</span>
                                </div>

                                <div class="flex items-center justify-between gap-3 border-t border-gray-100 pt-3 text-xs text-gray-400">
                                    <span>{{ $ticket->created_at->translatedFormat('d M Y') }}</span>
                                    <span class="flex items-center gap-1">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" /></svg>
                                        {{ $ticket->messages_count }} {{ __('msg') }}
                                    </span>
                                </div>
                            </a>
                        @endforeach

                        <div>
                            {{ $tickets->links() }}
                        </div>
                    @endif
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
