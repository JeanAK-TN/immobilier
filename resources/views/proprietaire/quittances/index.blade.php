<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">{{ __('Quittances PDF') }}</h2>
                <p class="mt-1 text-sm text-gray-500">{{ __('Historique des quittances générées à partir des paiements simulés réussis.') }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto grid max-w-7xl gap-6 px-4 sm:px-6 lg:px-8">
            <section class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                <form method="GET" action="{{ route('proprietaire.quittances.index') }}" class="grid gap-4 lg:grid-cols-3 lg:items-end">
                    <div class="grid gap-2">
                        <x-input-label for="contrat_id" :value="__('Contrat')" />
                        <select id="contrat_id" name="contrat_id" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">{{ __('Tous les contrats') }}</option>
                            @foreach ($contrats as $contrat)
                                <option value="{{ $contrat->id }}" @selected($filtres['contratId'] === (string) $contrat->id)>
                                    {{ $contrat->bien->nom }} — {{ $contrat->locataire->nomComplet() }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid gap-2">
                        <x-input-label for="periode" :value="__('Période')" />
                        <x-text-input id="periode" name="periode" type="month" class="w-full" :value="$filtres['periode']" />
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <x-primary-button>{{ __('Filtrer') }}</x-primary-button>
                        <a href="{{ route('proprietaire.quittances.index') }}" class="inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition hover:bg-gray-50">
                            {{ __('Réinitialiser') }}
                        </a>
                    </div>
                </form>
            </section>

            @if ($quittances->isEmpty())
                <section class="rounded-3xl border border-dashed border-gray-300 bg-white p-10 text-center shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('Aucune quittance générée') }}</h3>
                    <p class="mt-2 text-sm text-gray-500">{{ __('Générez une quittance depuis la fiche d\'un paiement simulé réussi.') }}</p>
                </section>
            @else
                <section class="grid gap-5 lg:grid-cols-2 xl:grid-cols-3">
                    @foreach ($quittances as $quittance)
                        <article class="grid gap-4 rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                            <div>
                                <p class="text-lg font-semibold text-gray-900">{{ $quittance->numero_quittance }}</p>
                                <p class="mt-1 text-sm text-gray-500">{{ $quittance->contrat->bien->nom }} — {{ $quittance->contrat->locataire->nomComplet() }}</p>
                            </div>

                            <div class="grid gap-2 text-sm text-gray-600">
                                <p>{{ __('Période : :periode', ['periode' => $quittance->labelPeriode()]) }}</p>
                                <p>{{ __('Montant :') }} <x-money :amount="$quittance->paiement->montant" /></p>
                                <p>{{ __('Émise le :date', ['date' => $quittance->emise_le?->translatedFormat('d/m/Y')]) }}</p>
                            </div>

                            <a
                                href="{{ route('proprietaire.quittances.download', $quittance) }}"
                                class="inline-flex items-center justify-center rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50"
                            >
                                {{ __('Télécharger le PDF') }}
                            </a>
                        </article>
                    @endforeach
                </section>

                <div>
                    {{ $quittances->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
