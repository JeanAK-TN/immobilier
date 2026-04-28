<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <span @class([
                        'rounded-full px-3 py-1 text-xs font-medium ring-1 ring-inset',
                        'bg-amber-50 text-amber-700 ring-amber-200' => $bien->estOccupeActuellement(),
                        'bg-emerald-50 text-emerald-700 ring-emerald-200' => ! $bien->estOccupeActuellement(),
                    ])>
                        {{ $bien->occupationLabel() }}
                    </span>

                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-700 ring-1 ring-inset ring-slate-200">
                        {{ $bien->statut->label() }}
                    </span>
                </div>

                <h2 class="mt-3 text-xl font-semibold leading-tight text-gray-900">
                    {{ $bien->nom }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ $bien->type->label() }} · {{ $bien->adresse }}, {{ $bien->ville }} — {{ $bien->pays }}
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a
                    href="{{ route('proprietaire.biens.edit', $bien) }}"
                    class="inline-flex items-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-700"
                >
                    {{ __('Modifier') }}
                </a>

                <a
                    href="{{ route('proprietaire.biens.index') }}"
                    class="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50"
                >
                    {{ __('Retour à la liste') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto grid max-w-7xl gap-6 px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3.5 text-sm font-medium text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            <div class="grid gap-6 xl:grid-cols-[minmax(0,2fr)_minmax(320px,1fr)]">
                <section class="grid gap-6 content-start">
                    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                        <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Type') }}</p>
                                <p class="mt-2 text-sm font-semibold text-gray-900">{{ $bien->type->label() }}</p>
                            </div>

                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Statut déclaré') }}</p>
                                <p class="mt-2 text-sm font-semibold text-gray-900">{{ $bien->statut->label() }}</p>
                            </div>

                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Occupation actuelle') }}</p>
                                <p class="mt-2 text-sm font-semibold text-gray-900">{{ $bien->occupationLabel() }}</p>
                            </div>

                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Photos') }}</p>
                                <p class="mt-2 text-sm font-semibold text-gray-900">{{ $bien->photos_count }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('Informations du bien') }}</h3>

                        <dl class="mt-5 grid gap-5 md:grid-cols-2">
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Adresse') }}</dt>
                                <dd class="mt-1.5 text-sm text-gray-700">{{ $bien->adresse }}</dd>
                            </div>

                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Ville') }}</dt>
                                <dd class="mt-1.5 text-sm text-gray-700">{{ $bien->ville }}</dd>
                            </div>

                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Pays') }}</dt>
                                <dd class="mt-1.5 text-sm text-gray-700">{{ $bien->pays }}</dd>
                            </div>

                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Créé le') }}</dt>
                                <dd class="mt-1.5 text-sm text-gray-700">{{ $bien->created_at->translatedFormat('d F Y') }}</dd>
                            </div>

                            <div class="md:col-span-2">
                                <dt class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Description') }}</dt>
                                <dd class="mt-1.5 text-sm leading-6 text-gray-700">
                                    {{ $bien->description ?: __('Aucune description renseignée pour le moment.') }}
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ __('Galerie photos') }}</h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    {{ __('Les photos sont stockées dans les pièces jointes du bien.') }}
                                </p>
                            </div>

                            <a
                                href="{{ route('proprietaire.biens.edit', $bien) }}"
                                class="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50"
                            >
                                {{ __('Gérer les photos') }}
                            </a>
                        </div>

                        @if ($bien->photos->isEmpty())
                            <div class="mt-6 rounded-xl border border-dashed border-gray-200 bg-slate-50 px-6 py-12 text-center text-sm text-gray-400">
                                {{ __('Aucune photo n\'a encore été ajoutée à ce bien.') }}
                            </div>
                        @else
                            <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                                @foreach ($bien->photos as $photo)
                                    <figure class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                                        <img
                                            src="{{ $photo->url() }}"
                                            alt="{{ $photo->nom_original }}"
                                            class="h-56 w-full object-cover"
                                        >

                                        <figcaption class="grid gap-0.5 p-4">
                                            <p class="truncate text-sm font-medium text-gray-900">{{ $photo->nom_original }}</p>
                                            <p class="text-xs text-gray-400">{{ $photo->tailleFormatee() }}</p>
                                        </figcaption>
                                    </figure>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </section>

                <aside class="grid gap-6 content-start">
                    <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('Occupation & contrat') }}</h3>

                        @if ($contratActif)
                            <div class="mt-5 grid gap-4 rounded-2xl bg-amber-50 p-5 text-sm text-amber-900">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wider text-amber-600">{{ __('Contrat actif') }}</p>
                                    <p class="mt-1.5 font-semibold">{{ __('Oui') }}</p>
                                </div>

                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wider text-amber-600">{{ __('Locataire') }}</p>
                                    <p class="mt-1.5 font-semibold">{{ $contratActif->locataire->nomComplet() }}</p>
                                </div>

                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wider text-amber-600">{{ __('Début du contrat') }}</p>
                                    <p class="mt-1.5 font-semibold">{{ $contratActif->date_debut->translatedFormat('d F Y') }}</p>
                                </div>
                            </div>
                        @else
                            <div class="mt-5 rounded-2xl bg-emerald-50 p-5 text-sm text-emerald-900">
                                <p class="font-semibold">{{ __('Aucun contrat actif') }}</p>
                                <p class="mt-2 text-emerald-700">{{ __('Ce bien apparaît actuellement comme libre de tout contrat actif.') }}</p>
                            </div>
                        @endif
                    </section>

                    @can('delete', $bien)
                        <section class="rounded-2xl border border-red-200 bg-white p-6 shadow-sm">
                            <h3 class="text-lg font-semibold text-gray-900">{{ __('Suppression') }}</h3>
                            <p class="mt-2 text-sm text-gray-500">
                                {{ __('Supprimez ce bien uniquement s\'il n\'est lié à aucun contrat.') }}
                            </p>

                            <form method="POST" action="{{ route('proprietaire.biens.destroy', $bien) }}" class="mt-5">
                                @csrf
                                @method('DELETE')

                                <x-danger-button onclick="return confirm('Confirmer la suppression de ce bien ?')">
                                    {{ __('Supprimer le bien') }}
                                </x-danger-button>
                            </form>
                        </section>
                    @endcan
                </aside>
            </div>
        </div>
    </div>
</x-app-layout>
