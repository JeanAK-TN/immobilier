<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    {{ $ticket->titre }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Locataire : :locataire', ['locataire' => $ticket->contrat->locataire->nomComplet()]) }}
                </p>
            </div>

            <a
                href="{{ route('proprietaire.tickets.index') }}"
                class="inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50"
            >
                {{ __('Retour aux tickets') }}
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto grid max-w-7xl gap-6 px-4 sm:px-6 lg:px-8 xl:grid-cols-[minmax(0,1.2fr)_minmax(340px,0.8fr)]">
            <div class="grid gap-6">
                @if (session('status'))
                    <div class="rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                        {{ session('status') }}
                    </div>
                @endif

                <section class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="flex flex-wrap items-center gap-3">
                        <x-tickets.status-badge :status="$ticket->statut" />
                        <x-tickets.priority-badge :priority="$ticket->priorite" />
                        <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600">
                            {{ $ticket->categorie->label() }}
                        </span>
                    </div>

                    <div class="mt-5 rounded-2xl bg-gray-50 p-5">
                        <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">{{ __('Description initiale') }}</h3>
                        <p class="mt-3 whitespace-pre-line text-sm leading-7 text-gray-700">{{ $ticket->description }}</p>
                    </div>

                    @if ($ticket->piecesJointes->isNotEmpty())
                        <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                            @foreach ($ticket->piecesJointes as $pieceJointe)
                                <a href="{{ $pieceJointe->url() }}" target="_blank" class="overflow-hidden rounded-2xl border border-gray-200 bg-gray-50">
                                    <img src="{{ $pieceJointe->url() }}" alt="{{ $pieceJointe->nom_original }}" class="h-48 w-full object-cover" />
                                    <div class="px-4 py-3 text-xs text-gray-500">
                                        {{ $pieceJointe->nom_original }}
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </section>

                <section class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('Fil de discussion') }}</h3>
                        <p class="mt-1 text-sm text-gray-500">{{ __('Répondez au locataire ou ajoutez une note interne visible uniquement côté propriétaire.') }}</p>
                    </div>

                    @if ($ticket->messages->isEmpty())
                        <div class="mt-6 rounded-2xl border border-dashed border-gray-300 px-6 py-10 text-center text-sm text-gray-500">
                            {{ __('Aucun message pour le moment.') }}
                        </div>
                    @else
                        <div class="mt-6 grid gap-4">
                            @foreach ($ticket->messages as $message)
                                <article class="rounded-2xl border p-5 {{ $message->est_note_interne ? 'border-amber-200 bg-amber-50' : 'border-gray-200 bg-white' }}">
                                    <div class="flex flex-wrap items-center justify-between gap-3">
                                        <div class="flex items-center gap-2">
                                            <p class="font-semibold text-gray-900">{{ $message->auteur->name }}</p>
                                            @if ($message->est_note_interne)
                                                <span class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-xs font-medium text-amber-800">
                                                    {{ __('Note interne') }}
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-xs uppercase tracking-wide text-gray-400">{{ $message->created_at->translatedFormat('d M Y H:i') }}</p>
                                    </div>
                                    <p class="mt-3 whitespace-pre-line text-sm leading-7 text-gray-700">{{ $message->message }}</p>
                                </article>
                            @endforeach
                        </div>
                    @endif

                    @can('reply', $ticket)
                        <form method="POST" action="{{ route('proprietaire.tickets.messages.store', $ticket) }}" class="mt-6 grid gap-4">
                            @csrf

                            <div>
                                <x-input-label for="message" :value="__('Réponse ou note')" />
                                <textarea
                                    id="message"
                                    name="message"
                                    rows="4"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required
                                >{{ old('message') }}</textarea>
                                <x-input-error :messages="$errors->get('message')" class="mt-2" />
                            </div>

                            <label class="inline-flex items-center gap-3 text-sm text-gray-700">
                                <input type="checkbox" name="est_note_interne" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" @checked(old('est_note_interne')) />
                                <span>{{ __('Enregistrer comme note interne (non visible par le locataire)') }}</span>
                            </label>

                            <div class="flex flex-wrap gap-3">
                                <x-primary-button class="justify-center sm:w-fit">
                                    {{ __('Ajouter le message') }}
                                </x-primary-button>
                            </div>
                        </form>
                    @else
                        <div class="mt-6 rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-600">
                            {{ __('Ce ticket est fermé et ne peut plus recevoir de nouveaux messages.') }}
                        </div>
                    @endcan
                </section>
            </div>

            <aside class="grid gap-6">
                <section class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('Traitement') }}</h3>

                    @can('changeStatus', $ticket)
                        <form method="POST" action="{{ route('proprietaire.tickets.update', $ticket) }}" class="mt-5 grid gap-4">
                            @csrf
                            @method('PATCH')

                            <div>
                                <x-input-label for="statut" :value="__('Changer le statut')" />
                                <select id="statut" name="statut" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @foreach ($statutOptions as $statutOption)
                                        <option value="{{ $statutOption->value }}" @selected(old('statut', $ticket->statut->value) === $statutOption->value)>
                                            {{ $statutOption->label() }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('statut')" class="mt-2" />
                            </div>

                            <x-primary-button class="justify-center">
                                {{ __('Mettre à jour le statut') }}
                            </x-primary-button>
                        </form>
                    @endcan
                </section>

                <section class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('Informations du ticket') }}</h3>

                    <dl class="mt-5 grid gap-4 text-sm text-gray-700">
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-gray-500">{{ __('Bien') }}</dt>
                            <dd class="mt-2 font-semibold text-gray-900">{{ $ticket->contrat->bien->nom }}</dd>
                        </div>

                        <div>
                            <dt class="text-xs uppercase tracking-wide text-gray-500">{{ __('Adresse') }}</dt>
                            <dd class="mt-2">{{ $ticket->contrat->bien->adresse }}, {{ $ticket->contrat->bien->ville }}</dd>
                        </div>

                        <div>
                            <dt class="text-xs uppercase tracking-wide text-gray-500">{{ __('Locataire') }}</dt>
                            <dd class="mt-2">{{ $ticket->contrat->locataire->nomComplet() }} ({{ $ticket->contrat->locataire->email }})</dd>
                        </div>

                        <div>
                            <dt class="text-xs uppercase tracking-wide text-gray-500">{{ __('Créé le') }}</dt>
                            <dd class="mt-2">{{ $ticket->created_at->translatedFormat('d F Y à H:i') }}</dd>
                        </div>

                        <div>
                            <dt class="text-xs uppercase tracking-wide text-gray-500">{{ __('Mis à jour le') }}</dt>
                            <dd class="mt-2">{{ $ticket->updated_at->translatedFormat('d F Y à H:i') }}</dd>
                        </div>
                    </dl>
                </section>
            </aside>
        </div>
    </div>
</x-app-layout>
