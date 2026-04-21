<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    {{ __('Paiements simulés') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Suivez toutes les simulations de paiement de vos locataires, par bien, période et statut.') }}
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto grid max-w-7xl gap-6 px-4 sm:px-6 lg:px-8">
            <div class="rounded-3xl border border-amber-200 bg-amber-50 p-5 text-sm text-amber-900">
                <p class="font-semibold">{{ __('Paiement simulé - aucune transaction réelle') }}</p>
                <p class="mt-2">{{ __('Tous les paiements affichés ici sont des simulations automatiquement validées comme réussies lors de leur création.') }}</p>
            </div>

            <section class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                <form method="GET" action="{{ route('proprietaire.paiements.index') }}" class="grid gap-4 lg:grid-cols-4 lg:items-end">
                    <div class="grid gap-2">
                        <x-input-label for="bien_id" :value="__('Bien')" />
                        <select id="bien_id" name="bien_id" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">{{ __('Tous les biens') }}</option>
                            @foreach ($biens as $bien)
                                <option value="{{ $bien->id }}" @selected($filtres['bienId'] === (string) $bien->id)>{{ $bien->nom }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid gap-2">
                        <x-input-label for="locataire_id" :value="__('Locataire')" />
                        <select id="locataire_id" name="locataire_id" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">{{ __('Tous les locataires') }}</option>
                            @foreach ($locataires as $locataire)
                                <option value="{{ $locataire->id }}" @selected($filtres['locataireId'] === (string) $locataire->id)>{{ $locataire->nomComplet() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid gap-2">
                        <x-input-label for="periode" :value="__('Période')" />
                        <x-text-input id="periode" name="periode" type="month" class="w-full" :value="$filtres['periode']" />
                    </div>

                    <div class="grid gap-2">
                        <x-input-label for="statut" :value="__('Statut')" />
                        <select id="statut" name="statut" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">{{ __('Tous les statuts') }}</option>
                            @foreach ($statutOptions as $statutOption)
                                <option value="{{ $statutOption->value }}" @selected($filtres['statut'] === $statutOption->value)>{{ $statutOption->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex flex-wrap items-center gap-3 lg:col-span-4">
                        <x-primary-button>{{ __('Filtrer') }}</x-primary-button>
                        <a
                            href="{{ route('proprietaire.paiements.index') }}"
                            class="inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition hover:bg-gray-50"
                        >
                            {{ __('Réinitialiser') }}
                        </a>
                    </div>
                </form>
            </section>

            @if ($paiements->isEmpty())
                <section class="rounded-3xl border border-dashed border-gray-300 bg-white p-10 text-center shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('Aucun paiement trouvé') }}</h3>
                    <p class="mt-2 text-sm text-gray-500">{{ __('Ajustez les filtres ou attendez qu\'un locataire enregistre une nouvelle simulation.') }}</p>
                </section>
            @else
                <section class="grid gap-5 lg:grid-cols-2 xl:grid-cols-3">
                    @foreach ($paiements as $paiement)
                        <article class="grid gap-4 rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-lg font-semibold text-gray-900">{{ $paiement->contrat->locataire->nomComplet() }}</p>
                                    <p class="mt-1 text-sm text-gray-500">{{ $paiement->contrat->bien->nom }}</p>
                                    <p class="mt-1 text-xs uppercase tracking-wide text-gray-400">{{ $paiement->reference }}</p>
                                </div>

                                <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-200">
                                    {{ $paiement->statut->label() }}
                                </span>
                            </div>

                            <div class="grid gap-2 text-sm text-gray-600">
                                <p>{{ __('Période : :periode', ['periode' => $paiement->labelPeriode()]) }}</p>
                                <p>{{ __('Mode : :mode', ['mode' => $paiement->modeLabel()]) }}</p>
                                <p>{{ __('Montant :') }} <x-money :amount="$paiement->montant" /></p>
                            </div>

                            <a
                                href="{{ route('proprietaire.paiements.show', $paiement) }}"
                                class="inline-flex items-center justify-center rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50"
                            >
                                {{ __('Voir le reçu') }}
                            </a>
                        </article>
                    @endforeach
                </section>

                <div>
                    {{ $paiements->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
