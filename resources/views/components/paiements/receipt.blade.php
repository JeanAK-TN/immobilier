@props([
    'paiement',
])

<section class="grid gap-6">
    <div class="flex items-start gap-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3.5 text-sm text-amber-900">
        <span class="mt-0.5 shrink-0 text-amber-500">⚠</span>
        <div>
            <p class="font-semibold">{{ __('Paiement simulé - aucune transaction réelle') }}</p>
            <p class="mt-1 text-amber-800">{{ __('Ce reçu correspond à une simulation validée automatiquement dans l\'application. Aucun encaissement réel n\'a été effectué.') }}</p>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Référence') }}</p>
                <h3 class="mt-2 font-mono text-2xl font-semibold text-gray-900">{{ $paiement->reference }}</h3>
                <p class="mt-2 text-sm text-gray-500">{{ __('Période : :periode', ['periode' => $paiement->labelPeriode()]) }}</p>
            </div>

            <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-200">
                {{ $paiement->statut->label() }}
            </span>
        </div>

        <dl class="mt-6 grid gap-5 md:grid-cols-2 xl:grid-cols-4">
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Montant') }}</dt>
                <dd class="mt-1.5 text-lg font-bold text-gray-900"><x-money :amount="$paiement->montant" /></dd>
            </div>

            <div>
                <dt class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Mode') }}</dt>
                <dd class="mt-1.5 text-sm text-gray-700">{{ $paiement->modeLabel() }}</dd>
            </div>

            <div>
                <dt class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Bien') }}</dt>
                <dd class="mt-1.5 text-sm text-gray-700">{{ $paiement->contrat->bien->nom }}</dd>
            </div>

            <div>
                <dt class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Locataire') }}</dt>
                <dd class="mt-1.5 text-sm text-gray-700">{{ $paiement->contrat->locataire->nomComplet() }}</dd>
            </div>
        </dl>

        <div class="mt-6 grid gap-4 rounded-xl bg-slate-50 p-5 text-sm md:grid-cols-2">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Date d\'enregistrement') }}</p>
                <p class="mt-1.5 font-semibold text-gray-900">{{ $paiement->created_at?->translatedFormat('d F Y H:i') }}</p>
            </div>

            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Commentaire système') }}</p>
                <p class="mt-1.5 font-semibold text-gray-900">{{ $paiement->notes ?? __('Aucune note complémentaire') }}</p>
            </div>
        </div>
    </div>
</section>
