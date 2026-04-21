@props([
    'paiement',
])

<section class="grid gap-6">
    <div class="rounded-3xl border border-amber-200 bg-amber-50 p-5 text-sm text-amber-900">
        <p class="font-semibold">{{ __('Paiement simulé - aucune transaction réelle') }}</p>
        <p class="mt-2">{{ __('Ce reçu correspond à une simulation validée automatiquement dans l\'application. Aucun encaissement réel n\'a été effectué.') }}</p>
    </div>

    <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">{{ __('Référence') }}</p>
                <h3 class="mt-2 text-2xl font-semibold text-gray-900">{{ $paiement->reference }}</h3>
                <p class="mt-2 text-sm text-gray-500">{{ __('Période : :periode', ['periode' => $paiement->labelPeriode()]) }}</p>
            </div>

            <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-200">
                {{ $paiement->statut->label() }}
            </span>
        </div>

        <dl class="mt-6 grid gap-6 md:grid-cols-2 xl:grid-cols-4">
            <div>
                <dt class="text-sm font-medium text-gray-500">{{ __('Montant') }}</dt>
                <dd class="mt-2 text-lg font-semibold text-gray-900"><x-money :amount="$paiement->montant" /></dd>
            </div>

            <div>
                <dt class="text-sm font-medium text-gray-500">{{ __('Mode') }}</dt>
                <dd class="mt-2 text-sm text-gray-900">{{ $paiement->modeLabel() }}</dd>
            </div>

            <div>
                <dt class="text-sm font-medium text-gray-500">{{ __('Bien') }}</dt>
                <dd class="mt-2 text-sm text-gray-900">{{ $paiement->contrat->bien->nom }}</dd>
            </div>

            <div>
                <dt class="text-sm font-medium text-gray-500">{{ __('Locataire') }}</dt>
                <dd class="mt-2 text-sm text-gray-900">{{ $paiement->contrat->locataire->nomComplet() }}</dd>
            </div>
        </dl>

        <div class="mt-6 grid gap-4 rounded-2xl bg-gray-50 p-5 text-sm text-gray-700 md:grid-cols-2">
            <div>
                <p class="text-xs uppercase tracking-wide text-gray-500">{{ __('Date d\'enregistrement') }}</p>
                <p class="mt-2 font-semibold text-gray-900">{{ $paiement->created_at?->translatedFormat('d F Y H:i') }}</p>
            </div>

            <div>
                <p class="text-xs uppercase tracking-wide text-gray-500">{{ __('Commentaire système') }}</p>
                <p class="mt-2 font-semibold text-gray-900">{{ $paiement->notes ?? __('Aucune note complémentaire') }}</p>
            </div>
        </div>
    </div>
</section>
