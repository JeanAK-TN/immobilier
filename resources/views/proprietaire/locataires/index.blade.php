<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-900">
                    {{ __('Locataires') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Créez, suivez et gérez les comptes locataires de votre parc immobilier.') }}
                </p>
            </div>

            <a
                href="{{ route('proprietaire.locataires.create') }}"
                class="inline-flex items-center justify-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-700"
            >
                {{ __('+ Nouveau locataire') }}
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
                    action="{{ route('proprietaire.locataires.index') }}"
                    x-data
                    @change="$root.submit()"
                    @input.debounce.500ms="$root.submit()"
                    class="grid gap-4 sm:grid-cols-[1fr_auto] sm:items-end"
                >
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="grid gap-2">
                            <x-input-label for="recherche" :value="__('Recherche')" />
                            <x-text-input
                                id="recherche"
                                name="recherche"
                                type="text"
                                class="w-full"
                                :value="$filtres['recherche']"
                                placeholder="{{ __('Nom, prénom, e-mail, téléphone...') }}"
                            />
                        </div>

                        <div class="grid gap-2">
                            <x-input-label for="statut_compte" :value="__('Statut du compte')" />
                            <select id="statut_compte" name="statut_compte" class="rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500">
                                <option value="">{{ __('Tous les comptes') }}</option>
                                <option value="actif" @selected($filtres['statutCompte'] === 'actif')>{{ __('Actifs') }}</option>
                                <option value="inactif" @selected($filtres['statutCompte'] === 'inactif')>{{ __('Inactifs') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <button type="submit" class="hidden" tabindex="-1" aria-hidden="true">{{ __('Filtrer') }}</button>
                        <a
                            href="{{ route('proprietaire.locataires.index') }}"
                            class="inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition hover:bg-gray-50"
                        >
                            {{ __('Réinitialiser') }}
                        </a>
                    </div>
                </form>
            </section>

            @if ($locataires->isEmpty())
                <section class="rounded-2xl border border-dashed border-gray-300 bg-white p-12 text-center shadow-sm">
                    <p class="text-4xl text-gray-300">👤</p>
                    <h3 class="mt-4 text-lg font-semibold text-gray-900">{{ __('Aucun locataire trouvé') }}</h3>
                    <p class="mt-2 text-sm text-gray-500">
                        {{ __('Ajoutez votre premier locataire ou ajustez les filtres pour élargir la recherche.') }}
                    </p>
                    <div class="mt-6">
                        <a
                            href="{{ route('proprietaire.locataires.create') }}"
                            class="inline-flex items-center justify-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-700"
                        >
                            {{ __('Créer un locataire') }}
                        </a>
                    </div>
                </section>
            @else
                <section class="grid gap-5 lg:grid-cols-2 xl:grid-cols-3">
                    @foreach ($locataires as $locataire)
                        <article class="flex flex-col gap-4 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition hover:shadow-md">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="truncate font-semibold text-gray-900">{{ $locataire->nomComplet() }}</p>
                                    <p class="mt-0.5 truncate text-sm text-gray-500">{{ $locataire->email }}</p>
                                </div>

                                <span @class([
                                    'shrink-0 rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset',
                                    'bg-emerald-50 text-emerald-700 ring-emerald-200' => $locataire->compteActif(),
                                    'bg-red-50 text-red-700 ring-red-200' => ! $locataire->compteActif(),
                                ])>
                                    {{ $locataire->compteActif() ? __('Actif') : __('Inactif') }}
                                </span>
                            </div>

                            <div class="text-sm text-gray-500">
                                <p>{{ $locataire->telephone ?: __('Téléphone non renseigné') }}</p>
                                <p class="mt-0.5">{{ __('Créé le :date', ['date' => $locataire->created_at->translatedFormat('d/m/Y')]) }}</p>
                            </div>

                            <div class="grid grid-cols-2 divide-x divide-gray-100 rounded-xl bg-slate-50 text-center">
                                <div class="p-3">
                                    <p class="text-xs text-gray-400">{{ __('Contrats') }}</p>
                                    <p class="mt-1 font-semibold text-gray-800">{{ $locataire->contrats_count }}</p>
                                </div>
                                <div class="p-3">
                                    <p class="text-xs text-gray-400">{{ __('Actifs') }}</p>
                                    <p class="mt-1 font-semibold text-gray-800">{{ $locataire->contrats_actifs_count }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 border-t border-gray-100 pt-4">
                                <a
                                    href="{{ route('proprietaire.locataires.show', $locataire) }}"
                                    class="inline-flex items-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-700"
                                >
                                    {{ __('Voir la fiche') }}
                                </a>
                                <a
                                    href="{{ route('proprietaire.locataires.edit', $locataire) }}"
                                    class="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50"
                                >
                                    {{ __('Modifier') }}
                                </a>
                            </div>
                        </article>
                    @endforeach
                </section>

                <div>
                    {{ $locataires->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
