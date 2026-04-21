<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-900">{{ $ticket->titre }}</h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Bien concerné : :bien', ['bien' => $ticket->contrat->bien->nom]) }}
                </p>
            </div>

            <a
                href="{{ route('locataire.tickets.index') }}"
                class="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50"
            >
                ← {{ __('Mes tickets') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="mb-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3.5 text-sm font-medium text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_minmax(280px,0.35fr)]">

                {{-- COLONNE PRINCIPALE : messagerie --}}
                <div class="flex flex-col gap-6">

                    {{-- Fil de discussion --}}
                    <section class="flex flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                        <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                            <h3 class="font-semibold text-gray-900">{{ __('Fil de discussion') }}</h3>
                            <div class="flex flex-wrap items-center gap-2">
                                <x-tickets.status-badge :status="$ticket->statut" />
                                <x-tickets.priority-badge :priority="$ticket->priorite" />
                            </div>
                        </div>

                        {{-- Messages scrollables --}}
                        <div
                            class="flex flex-col gap-3 overflow-y-auto px-6 py-5"
                            style="min-height: 240px; max-height: 55vh"
                            x-data
                            x-init="$el.scrollTop = $el.scrollHeight"
                        >
                            @if ($ticket->messages->isEmpty())
                                <div class="flex flex-1 items-center justify-center py-12 text-sm text-gray-400">
                                    {{ __('Aucun message pour le moment. Soyez le premier à écrire.') }}
                                </div>
                            @else
                                @foreach ($ticket->messages as $message)
                                    <article class="rounded-2xl border border-gray-200 p-4">
                                        <div class="flex items-center justify-between gap-3">
                                            <p class="text-sm font-semibold text-gray-900">{{ $message->auteur->name }}</p>
                                            <p class="text-xs text-gray-400">{{ $message->created_at->translatedFormat('d M Y à H:i') }}</p>
                                        </div>
                                        <p class="mt-2 whitespace-pre-line text-sm leading-relaxed text-gray-700">{{ $message->message }}</p>
                                    </article>
                                @endforeach
                            @endif
                        </div>

                        {{-- Réponse --}}
                        @can('reply', $ticket)
                            <div class="border-t border-gray-100 px-6 py-5">
                                <form method="POST" action="{{ route('locataire.tickets.messages.store', $ticket) }}" class="grid gap-3">
                                    @csrf
                                    <div>
                                        <x-input-label for="message" :value="__('Votre message')" />
                                        <textarea
                                            id="message"
                                            name="message"
                                            rows="3"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            placeholder="{{ __('Écrivez votre réponse…') }}"
                                            required
                                        >{{ old('message') }}</textarea>
                                        <x-input-error :messages="$errors->get('message')" class="mt-2" />
                                    </div>
                                    <div>
                                        <x-primary-button>{{ __('Envoyer') }}</x-primary-button>
                                    </div>
                                </form>
                            </div>
                        @else
                            <div class="border-t border-gray-100 px-6 py-4 text-sm text-gray-400">
                                {{ __('Ce ticket est fermé et ne peut plus recevoir de messages.') }}
                            </div>
                        @endcan
                    </section>

                </div>

                {{-- SIDEBAR : description + infos --}}
                <aside class="grid gap-4 content-start">

                    {{-- Description --}}
                    <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm" x-data="{ open: false }">
                        <button
                            type="button"
                            class="flex w-full items-center justify-between gap-2 text-left"
                            @click="open = !open"
                        >
                            <span class="text-sm font-semibold text-gray-900">{{ __('Description initiale') }}</span>
                            <span class="text-gray-400 transition-transform" :class="{ 'rotate-180': open }">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg>
                            </span>
                        </button>

                        <div x-show="open" x-cloak x-transition class="mt-4">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600">{{ $ticket->categorie->label() }}</span>
                            </div>
                            <p class="mt-3 whitespace-pre-line text-sm leading-relaxed text-gray-600">{{ $ticket->description }}</p>

                            @if ($ticket->piecesJointes->isNotEmpty())
                                <div class="mt-4 grid gap-2 sm:grid-cols-2">
                                    @foreach ($ticket->piecesJointes as $pieceJointe)
                                        <a href="{{ $pieceJointe->url() }}" target="_blank" class="overflow-hidden rounded-xl border border-gray-200">
                                            <img src="{{ $pieceJointe->url() }}" alt="{{ $pieceJointe->nom_original }}" class="h-32 w-full object-cover" />
                                            <div class="px-3 py-2 text-xs text-gray-500 truncate">{{ $pieceJointe->nom_original }}</div>
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </section>

                    {{-- Informations --}}
                    <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                        <h3 class="text-sm font-semibold text-gray-900">{{ __('Informations') }}</h3>

                        <dl class="mt-4 grid gap-3 text-sm">
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('Statut') }}</dt>
                                <dd class="mt-1 flex">
                                    <x-tickets.status-badge :status="$ticket->statut" />
                                </dd>
                            </div>

                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('Priorité') }}</dt>
                                <dd class="mt-1 flex">
                                    <x-tickets.priority-badge :priority="$ticket->priorite" />
                                </dd>
                            </div>

                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('Adresse du bien') }}</dt>
                                <dd class="mt-1 text-gray-700">{{ $ticket->contrat->bien->adresse }}, {{ $ticket->contrat->bien->ville }}</dd>
                            </div>

                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('Créé le') }}</dt>
                                <dd class="mt-1 text-gray-700">{{ $ticket->created_at->translatedFormat('d F Y') }}</dd>
                            </div>

                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('Mis à jour') }}</dt>
                                <dd class="mt-1 text-gray-700">{{ $ticket->updated_at->translatedFormat('d F Y') }}</dd>
                            </div>
                        </dl>
                    </section>

                </aside>
            </div>
        </div>
    </div>
</x-app-layout>
