<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    {{ __('Contrats') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Suivez vos baux, leur statut de signature et les documents associés.') }}
                </p>
            </div>

            <a
                href="{{ route('proprietaire.contrats.create') }}"
                class="inline-flex items-center justify-center rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-700"
            >
                {{ __('Nouveau contrat') }}
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto grid max-w-7xl gap-6 px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            <section class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                <form method="GET" action="{{ route('proprietaire.contrats.index') }}" class="grid gap-4 lg:grid-cols-5 lg:items-end">
                    <div class="grid gap-2 lg:col-span-2">
                        <x-input-label for="recherche" :value="__('Recherche')" />
                        <x-text-input
                            id="recherche"
                            name="recherche"
                            type="text"
                            class="w-full"
                            :value="$filtres['recherche']"
                            placeholder="Bien, adresse, locataire..."
                        />
                    </div>

                    <div class="grid gap-2">
                        <x-input-label for="statut" :value="__('Statut')" />
                        <select id="statut" name="statut" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">{{ __('Tous les statuts') }}</option>
                            @foreach ($statutOptions as $statutOption)
                                <option value="{{ $statutOption->value }}" @selected($filtres['statut'] === $statutOption->value)>
                                    {{ $statutOption->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid gap-2">
                        <x-input-label for="bien_id" :value="__('Bien')" />
                        <select id="bien_id" name="bien_id" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">{{ __('Tous les biens') }}</option>
                            @foreach ($biens as $bien)
                                <option value="{{ $bien->id }}" @selected($filtres['bienId'] === (string) $bien->id)>
                                    {{ $bien->nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid gap-2">
                        <x-input-label for="locataire_id" :value="__('Locataire')" />
                        <select id="locataire_id" name="locataire_id" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">{{ __('Tous les locataires') }}</option>
                            @foreach ($locataires as $locataire)
                                <option value="{{ $locataire->id }}" @selected($filtres['locataireId'] === (string) $locataire->id)>
                                    {{ $locataire->nomComplet() }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex flex-wrap items-center gap-3 lg:col-span-5">
                        <x-primary-button>{{ __('Filtrer') }}</x-primary-button>

                        <a
                            href="{{ route('proprietaire.contrats.index') }}"
                            class="inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition hover:bg-gray-50"
                        >
                            {{ __('Réinitialiser') }}
                        </a>
                    </div>
                </form>
            </section>

            @if ($contrats->isEmpty())
                <section class="rounded-3xl border border-dashed border-gray-300 bg-white p-10 text-center shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('Aucun contrat trouvé') }}</h3>
                    <p class="mt-2 text-sm text-gray-500">
                        {{ __('Ajoutez votre premier contrat ou élargissez les filtres pour voir davantage de résultats.') }}
                    </p>
                    <div class="mt-6">
                        <a
                            href="{{ route('proprietaire.contrats.create') }}"
                            class="inline-flex items-center justify-center rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-700"
                        >
                            {{ __('Créer un contrat') }}
                        </a>
                    </div>
                </section>
            @else
                <section class="grid gap-5 lg:grid-cols-2 xl:grid-cols-3">
                    @foreach ($contrats as $contrat)
                        <article class="grid gap-5 rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                            <div class="flex items-start justify-between gap-4">
                                <div class="min-w-0">
                                    <p class="truncate text-lg font-semibold text-gray-900">{{ $contrat->bien->nom }}</p>
                                    <p class="mt-1 truncate text-sm text-gray-500">{{ $contrat->locataire->nomComplet() }}</p>
                                </div>

                                <span class="rounded-full bg-slate-50 px-3 py-1 text-xs font-medium text-slate-700 ring-1 ring-inset ring-slate-200">
                                    {{ $contrat->statut->label() }}
                                </span>
                            </div>

                            <div class="grid gap-2 text-sm text-gray-600">
                                <p>{{ __('Début : :date', ['date' => $contrat->date_debut->translatedFormat('d/m/Y')]) }}</p>
                                <p>{{ __('Loyer : :montant €', ['montant' => number_format((float) $contrat->loyer_mensuel, 2, ',', ' ')]) }}</p>
                                <p>{{ $contrat->documentDisponible() ? __('Document PDF disponible') : __('Aucun document PDF') }}</p>
                            </div>

                            <div class="grid gap-3 rounded-2xl bg-gray-50 p-4 text-sm text-gray-600 sm:grid-cols-2">
                                <div>
                                    <p class="text-xs uppercase tracking-wide text-gray-500">{{ __('Bien') }}</p>
                                    <p class="mt-1 font-semibold text-gray-900">{{ $contrat->bien->ville }}</p>
                                </div>

                                <div>
                                    <p class="text-xs uppercase tracking-wide text-gray-500">{{ __('Signature') }}</p>
                                    <p class="mt-1 font-semibold text-gray-900">{{ $contrat->signatureLabel() }}</p>
                                </div>
                            </div>

                            <div class="flex flex-wrap items-center gap-3">
                                <a
                                    href="{{ route('proprietaire.contrats.show', $contrat) }}"
                                    class="inline-flex items-center rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-700"
                                >
                                    {{ __('Voir la fiche') }}
                                </a>

                                <a
                                    href="{{ route('proprietaire.contrats.edit', $contrat) }}"
                                    class="inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50"
                                >
                                    {{ __('Modifier') }}
                                </a>
                            </div>
                        </article>
                    @endforeach
                </section>

                <div>
                    {{ $contrats->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
