<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-900">
                    {{ __('Biens immobiliers') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Consultez, filtrez et gérez votre parc immobilier.') }}
                </p>
            </div>

            <a
                href="{{ route('proprietaire.biens.create') }}"
                class="inline-flex items-center justify-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-700"
            >
                {{ __('+ Nouveau bien') }}
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
                <form method="GET" action="{{ route('proprietaire.biens.index') }}" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5 lg:items-end">
                    <div class="grid gap-2 sm:col-span-2 lg:col-span-2">
                        <x-input-label for="recherche" :value="__('Recherche')" />
                        <x-text-input
                            id="recherche"
                            name="recherche"
                            type="text"
                            class="w-full"
                            :value="$filtres['recherche']"
                            placeholder="{{ __('Nom, adresse, ville...') }}"
                        />
                    </div>

                    <div class="grid gap-2">
                        <x-input-label for="type" :value="__('Type')" />
                        <select id="type" name="type" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">{{ __('Tous les types') }}</option>
                            @foreach ($typeOptions as $typeOption)
                                <option value="{{ $typeOption->value }}" @selected($filtres['type'] === $typeOption->value)>
                                    {{ $typeOption->label() }}
                                </option>
                            @endforeach
                        </select>
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
                        <x-input-label for="occupation" :value="__('Occupation')" />
                        <select id="occupation" name="occupation" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">{{ __('Toutes') }}</option>
                            <option value="disponible" @selected($filtres['occupation'] === 'disponible')>{{ __('Disponible') }}</option>
                            <option value="occupe" @selected($filtres['occupation'] === 'occupe')>{{ __('Occupé') }}</option>
                        </select>
                    </div>

                    <div class="flex flex-wrap items-center gap-3 sm:col-span-2 lg:col-span-5">
                        <x-primary-button>{{ __('Filtrer') }}</x-primary-button>
                        <a
                            href="{{ route('proprietaire.biens.index') }}"
                            class="inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition hover:bg-gray-50"
                        >
                            {{ __('Réinitialiser') }}
                        </a>
                        <p class="text-sm text-gray-400">
                            {{ $biens->total() }} {{ $biens->total() > 1 ? __('biens') : __('bien') }}
                        </p>
                    </div>
                </form>
            </section>

            @if ($biens->isEmpty())
                <section class="rounded-2xl border border-dashed border-gray-300 bg-white p-12 text-center shadow-sm">
                    <p class="text-4xl text-gray-300">🏠</p>
                    <h3 class="mt-4 text-lg font-semibold text-gray-900">{{ __('Aucun bien trouvé') }}</h3>
                    <p class="mt-2 text-sm text-gray-500">
                        {{ __('Ajustez vos filtres ou créez votre premier bien pour démarrer.') }}
                    </p>
                    <div class="mt-6">
                        <a
                            href="{{ route('proprietaire.biens.create') }}"
                            class="inline-flex items-center justify-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-700"
                        >
                            {{ __('Créer un bien') }}
                        </a>
                    </div>
                </section>
            @else
                <section class="grid gap-5 lg:grid-cols-2 xl:grid-cols-3">
                    @foreach ($biens as $bien)
                        <article class="flex flex-col gap-4 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition hover:shadow-md">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="truncate font-semibold text-gray-900">{{ $bien->nom }}</p>
                                    <p class="mt-0.5 text-sm text-gray-500">{{ $bien->type->label() }}</p>
                                </div>

                                <div class="flex shrink-0 flex-wrap justify-end gap-2">
                                    <span @class([
                                        'rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset',
                                        'bg-amber-50 text-amber-700 ring-amber-200' => $bien->estOccupeActuellement(),
                                        'bg-emerald-50 text-emerald-700 ring-emerald-200' => ! $bien->estOccupeActuellement(),
                                    ])>
                                        {{ $bien->occupationLabel() }}
                                    </span>

                                    <span class="rounded-full bg-slate-50 px-2.5 py-0.5 text-xs font-medium text-slate-700 ring-1 ring-inset ring-slate-200">
                                        {{ $bien->statut->label() }}
                                    </span>
                                </div>
                            </div>

                            <div class="text-sm text-gray-500">
                                <p>{{ $bien->adresse }}</p>
                                <p>{{ $bien->ville }} · {{ $bien->pays }}</p>
                            </div>

                            <div class="grid grid-cols-3 divide-x divide-gray-100 rounded-xl bg-slate-50 text-center">
                                <div class="p-3">
                                    <p class="text-xs text-gray-400">{{ __('Photos') }}</p>
                                    <p class="mt-1 font-semibold text-gray-800">{{ $bien->photos_count }}</p>
                                </div>
                                <div class="p-3">
                                    <p class="text-xs text-gray-400">{{ __('Contrats') }}</p>
                                    <p class="mt-1 font-semibold text-gray-800">{{ $bien->contrat_actif_count }}</p>
                                </div>
                                <div class="p-3">
                                    <p class="text-xs text-gray-400">{{ __('Ville') }}</p>
                                    <p class="mt-1 truncate font-semibold text-gray-800" title="{{ $bien->ville }}">{{ $bien->ville }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 border-t border-gray-100 pt-4">
                                <a
                                    href="{{ route('proprietaire.biens.show', $bien) }}"
                                    class="inline-flex items-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-700"
                                >
                                    {{ __('Voir la fiche') }}
                                </a>
                                <a
                                    href="{{ route('proprietaire.biens.edit', $bien) }}"
                                    class="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50"
                                >
                                    {{ __('Modifier') }}
                                </a>
                            </div>
                        </article>
                    @endforeach
                </section>

                <div>
                    {{ $biens->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
