<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-900">
                    {{ __('Contrats') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Suivez vos baux, leur statut de signature et les documents associés.') }}
                </p>
            </div>

            <a
                href="{{ route('proprietaire.contrats.create') }}"
                class="inline-flex items-center justify-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-700"
            >
                {{ __('+ Nouveau contrat') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto grid max-w-7xl gap-6 px-4 sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3.5 text-sm font-medium text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Filtres --}}
            <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <form
                    method="GET"
                    action="{{ route('proprietaire.contrats.index') }}"
                    x-data
                    @change="$root.submit()"
                    @input.debounce.500ms="$root.submit()"
                    class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5 lg:items-end"
                >
                    <div class="grid gap-2 sm:col-span-2 lg:col-span-2">
                        <x-input-label for="recherche" :value="__('Recherche')" />
                        <x-text-input
                            id="recherche"
                            name="recherche"
                            type="text"
                            class="w-full"
                            :value="$filtres['recherche']"
                            placeholder="{{ __('Bien, adresse, locataire...') }}"
                        />
                    </div>

                    <div class="grid gap-2">
                        <x-input-label for="statut" :value="__('Statut')" />
                        <select id="statut" name="statut" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
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
                        <select id="bien_id" name="bien_id" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
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
                        <select id="locataire_id" name="locataire_id" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">{{ __('Tous les locataires') }}</option>
                            @foreach ($locataires as $locataire)
                                <option value="{{ $locataire->id }}" @selected($filtres['locataireId'] === (string) $locataire->id)>
                                    {{ $locataire->nomComplet() }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex flex-wrap items-center gap-3 sm:col-span-2 lg:col-span-5">
                        <button type="submit" class="hidden" tabindex="-1" aria-hidden="true">{{ __('Filtrer') }}</button>
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
                <section class="rounded-2xl border border-dashed border-gray-300 bg-white p-12 text-center shadow-sm">
                    <p class="text-4xl text-gray-300">📋</p>
                    <h3 class="mt-4 text-lg font-semibold text-gray-900">{{ __('Aucun contrat trouvé') }}</h3>
                    <p class="mt-2 text-sm text-gray-500">
                        {{ __('Ajoutez votre premier contrat ou élargissez les filtres pour voir davantage de résultats.') }}
                    </p>
                    <div class="mt-6">
                        <a
                            href="{{ route('proprietaire.contrats.create') }}"
                            class="inline-flex items-center justify-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-700"
                        >
                            {{ __('Créer un contrat') }}
                        </a>
                    </div>
                </section>
            @else
                <section class="grid gap-5 lg:grid-cols-2 xl:grid-cols-3">
                    @foreach ($contrats as $contrat)
                        <article class="flex flex-col gap-4 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition hover:shadow-md">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="truncate font-semibold text-gray-900">{{ $contrat->bien->nom }}</p>
                                    <p class="mt-0.5 truncate text-sm text-gray-500">{{ $contrat->locataire->nomComplet() }}</p>
                                </div>

                                <span @class([
                                    'shrink-0 rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset',
                                    'bg-emerald-50 text-emerald-700 ring-emerald-200' => $contrat->statut->value === 'actif',
                                    'bg-amber-50 text-amber-700 ring-amber-200' => $contrat->statut->value === 'en_attente',
                                    'bg-gray-100 text-gray-600 ring-gray-200' => ! in_array($contrat->statut->value, ['actif', 'en_attente']),
                                ])>
                                    {{ $contrat->statut->label() }}
                                </span>
                            </div>

                            <div class="text-sm text-gray-500">
                                <p>{{ __('Depuis le :date', ['date' => $contrat->date_debut->translatedFormat('d/m/Y')]) }}</p>
                                <p class="mt-0.5">
                                    {{ __('Loyer :') }} <span class="font-medium text-gray-800"><x-money :amount="$contrat->loyer_mensuel" /></span>
                                </p>
                            </div>

                            <div class="grid grid-cols-2 divide-x divide-gray-100 rounded-xl bg-slate-50 text-center">
                                <div class="p-3">
                                    <p class="text-xs text-gray-400">{{ __('Ville') }}</p>
                                    <p class="mt-1 truncate font-semibold text-gray-800">{{ $contrat->bien->ville }}</p>
                                </div>
                                <div class="p-3">
                                    <p class="text-xs text-gray-400">{{ __('Signature') }}</p>
                                    <p class="mt-1 truncate font-semibold text-gray-800">{{ $contrat->signatureLabel() }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 border-t border-gray-100 pt-4">
                                <a
                                    href="{{ route('proprietaire.contrats.show', $contrat) }}"
                                    class="inline-flex items-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-700"
                                >
                                    {{ __('Voir la fiche') }}
                                </a>
                                <a
                                    href="{{ route('proprietaire.contrats.edit', $contrat) }}"
                                    class="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50"
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
