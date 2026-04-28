<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-900">{{ $ticket->titre }}</h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ $ticket->contrat->bien->nom }} · {{ $ticket->contrat->locataire->nomComplet() }}
                </p>
            </div>

            <a
                href="{{ route('proprietaire.tickets.index') }}"
                class="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50"
            >
                ← {{ __('Tickets') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3.5 text-sm font-medium text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Zone 1 : aperçu complet du ticket --}}
            <section class="rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="flex flex-wrap items-center gap-2 border-b border-gray-100 px-6 py-4">
                    <x-tickets.status-badge :status="$ticket->statut" />
                    <x-tickets.priority-badge :priority="$ticket->priorite" />
                    <span class="rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600">{{ $ticket->categorie->label() }}</span>
                </div>

                <div class="grid gap-6 p-6 lg:grid-cols-[minmax(0,1fr)_minmax(200px,0.3fr)]">

                    {{-- Description --}}
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Description') }}</p>
                        <p class="mt-3 whitespace-pre-line text-sm leading-relaxed text-gray-700">{{ $ticket->description }}</p>

                        @if ($ticket->piecesJointes->isNotEmpty())
                            <div class="mt-5 grid gap-2 sm:grid-cols-3">
                                @foreach ($ticket->piecesJointes as $pieceJointe)
                                    <a href="{{ $pieceJointe->url() }}" target="_blank" class="overflow-hidden rounded-xl border border-gray-200 transition hover:border-gray-300">
                                        <img src="{{ $pieceJointe->url() }}" alt="{{ $pieceJointe->nom_original }}" class="h-32 w-full object-cover" />
                                        <div class="px-3 py-2 text-xs text-gray-500 truncate">{{ $pieceJointe->nom_original }}</div>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- Métadonnées --}}
                    <dl class="grid gap-4 border-t border-gray-100 pt-4 text-sm lg:border-l lg:border-t-0 lg:pl-6 lg:pt-0">
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Bien') }}</dt>
                            <dd class="mt-1 font-semibold text-gray-900">{{ $ticket->contrat->bien->nom }}</dd>
                            <dd class="text-xs text-gray-500">{{ $ticket->contrat->bien->adresse }}, {{ $ticket->contrat->bien->ville }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Locataire') }}</dt>
                            <dd class="mt-1 font-medium text-gray-900">{{ $ticket->contrat->locataire->nomComplet() }}</dd>
                            <dd class="text-xs text-gray-500">{{ $ticket->contrat->locataire->email }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Créé le') }}</dt>
                            <dd class="mt-1 text-gray-700">{{ $ticket->created_at->translatedFormat('d F Y à H:i') }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Mis à jour') }}</dt>
                            <dd class="mt-1 text-gray-700">{{ $ticket->updated_at->translatedFormat('d F Y à H:i') }}</dd>
                        </div>
                    </dl>
                </div>
            </section>

            {{-- Zone 2 : messagerie + traitement --}}
            <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_minmax(260px,0.36fr)]">

                {{-- Fil de discussion --}}
                <section class="flex flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-100 px-6 py-4">
                        <h3 class="font-semibold text-gray-900">{{ __('Fil de discussion') }}</h3>
                    </div>

                    <div
                        class="flex flex-col gap-3 px-6 py-5 {{ $ticket->messages->count() > 4 ? 'overflow-y-auto' : '' }}"
                        @if ($ticket->messages->count() > 4) style="max-height: 55vh" @endif
                        x-data
                        x-init="$el.scrollTop = $el.scrollHeight"
                    >
                        @if ($ticket->messages->isEmpty())
                            <div class="flex flex-1 items-center justify-center py-12 text-sm text-gray-400">
                                {{ __('Aucun message pour le moment.') }}
                            </div>
                        @else
                            @foreach ($ticket->messages as $message)
                                @php $estLocataire = $message->auteur->isLocataire(); @endphp
                                <article @class([
                                    'rounded-2xl border p-4',
                                    'border-amber-200 bg-amber-50' => $message->est_note_interne,
                                    'border-blue-100 bg-blue-50' => ! $message->est_note_interne && $estLocataire,
                                    'border-gray-200 bg-white' => ! $message->est_note_interne && ! $estLocataire,
                                ])>
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        <div class="flex items-center gap-2">
                                            <p class="text-sm font-semibold text-gray-900">{{ $message->auteur->name }}</p>
                                            @if ($message->est_note_interne)
                                                <span class="rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-semibold text-amber-700">
                                                    {{ __('Note interne') }}
                                                </span>
                                            @else
                                                <span @class([
                                                    'rounded-full px-2 py-0.5 text-[10px] font-semibold',
                                                    'bg-blue-100 text-blue-700' => $estLocataire,
                                                    'bg-slate-100 text-slate-600' => ! $estLocataire,
                                                ])>
                                                    {{ $estLocataire ? __('Locataire') : __('Propriétaire') }}
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-gray-400">{{ $message->created_at->translatedFormat('d M Y à H:i') }}</p>
                                    </div>
                                    <p class="mt-2 whitespace-pre-line text-sm leading-relaxed text-gray-700">{{ $message->message }}</p>
                                </article>
                            @endforeach
                        @endif
                    </div>

                    @can('reply', $ticket)
                        <div class="border-t border-gray-100 px-6 py-5">
                            <form method="POST" action="{{ route('proprietaire.tickets.messages.store', $ticket) }}" class="grid gap-3">
                                @csrf
                                <div>
                                    <x-input-label for="message" :value="__('Réponse ou note')" />
                                    <textarea
                                        id="message"
                                        name="message"
                                        rows="3"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500"
                                        placeholder="{{ __('Écrivez votre réponse ou note interne…') }}"
                                        required
                                    >{{ old('message') }}</textarea>
                                    <x-input-error :messages="$errors->get('message')" class="mt-2" />
                                </div>

                                <div class="flex flex-wrap items-center gap-4">
                                    <x-primary-button>{{ __('Envoyer') }}</x-primary-button>
                                    <label class="flex items-center gap-2 text-sm text-gray-600">
                                        <input type="checkbox" name="est_note_interne" value="1" class="rounded border-gray-300 text-gray-900 shadow-sm focus:ring-gray-500" @checked(old('est_note_interne')) />
                                        <span>{{ __('Note interne (non visible par le locataire)') }}</span>
                                    </label>
                                </div>
                            </form>
                        </div>
                    @else
                        <div class="border-t border-gray-100 px-6 py-4 text-sm text-gray-400">
                            {{ __('Ce ticket est fermé et ne peut plus recevoir de messages.') }}
                        </div>
                    @endcan
                </section>

                {{-- Traitement --}}
                <aside class="grid gap-4 content-start">
                    @can('changeStatus', $ticket)
                        <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                            <h3 class="text-sm font-semibold text-gray-900">{{ __('Traitement') }}</h3>

                            <form method="POST" action="{{ route('proprietaire.tickets.update', $ticket) }}" class="mt-4 grid gap-3">
                                @csrf
                                @method('PATCH')

                                <div>
                                    <x-input-label for="statut" :value="__('Changer le statut')" />
                                    <select id="statut" name="statut" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500">
                                        @foreach ($statutOptions as $statutOption)
                                            <option value="{{ $statutOption->value }}" @selected(old('statut', $ticket->statut->value) === $statutOption->value)>
                                                {{ $statutOption->label() }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('statut')" class="mt-2" />
                                </div>

                                <x-primary-button class="justify-center">
                                    {{ __('Mettre à jour') }}
                                </x-primary-button>
                            </form>
                        </section>
                    @endcan

                    <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                        <h3 class="text-sm font-semibold text-gray-900">{{ __('Récapitulatif') }}</h3>
                        <dl class="mt-4 grid gap-3 text-sm">
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Statut actuel') }}</dt>
                                <dd class="mt-1 flex"><x-tickets.status-badge :status="$ticket->statut" /></dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Priorité') }}</dt>
                                <dd class="mt-1 flex"><x-tickets.priority-badge :priority="$ticket->priorite" /></dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Catégorie') }}</dt>
                                <dd class="mt-1 text-gray-700">{{ $ticket->categorie->label() }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Messages') }}</dt>
                                <dd class="mt-1 text-gray-700">{{ $ticket->messages->count() }}</dd>
                            </div>
                        </dl>
                    </section>
                </aside>

            </div>
        </div>
    </div>
</x-app-layout>
