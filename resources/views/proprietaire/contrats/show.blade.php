<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-700 ring-1 ring-inset ring-slate-200">
                        {{ $contrat->statut->label() }}
                    </span>

                    <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-200">
                        {{ $contrat->signatureLabel() }}
                    </span>
                </div>

                <h2 class="mt-3 text-xl font-semibold leading-tight text-gray-900">
                    {{ __('Contrat — :bien', ['bien' => $contrat->bien->nom]) }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ $contrat->locataire->nomComplet() }} · {{ $contrat->date_debut->translatedFormat('d F Y') }}
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a
                    href="{{ route('proprietaire.contrats.edit', $contrat) }}"
                    class="inline-flex items-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-700"
                >
                    {{ __('Modifier') }}
                </a>

                <a
                    href="{{ route('proprietaire.contrats.index') }}"
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
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('Données principales') }}</h3>

                        <dl class="mt-5 grid gap-5 md:grid-cols-2">
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Bien') }}</dt>
                                <dd class="mt-1.5 text-sm">
                                    <a href="{{ route('proprietaire.biens.show', $contrat->bien) }}" class="font-semibold text-gray-900 hover:text-gray-700 hover:underline underline-offset-2">
                                        {{ $contrat->bien->nom }}
                                    </a>
                                </dd>
                            </div>

                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Locataire') }}</dt>
                                <dd class="mt-1.5 text-sm">
                                    <a href="{{ route('proprietaire.locataires.show', $contrat->locataire) }}" class="font-semibold text-gray-900 hover:text-gray-700 hover:underline underline-offset-2">
                                        {{ $contrat->locataire->nomComplet() }}
                                    </a>
                                </dd>
                            </div>

                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Date de début') }}</dt>
                                <dd class="mt-1.5 text-sm text-gray-700">{{ $contrat->date_debut->translatedFormat('d F Y') }}</dd>
                            </div>

                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Date de fin') }}</dt>
                                <dd class="mt-1.5 text-sm text-gray-700">
                                    {{ $contrat->date_fin?->translatedFormat('d F Y') ?? __('Non définie') }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Jour de paiement') }}</dt>
                                <dd class="mt-1.5 text-sm text-gray-700">{{ $contrat->jour_paiement }}</dd>
                            </div>

                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Reconduction automatique') }}</dt>
                                <dd class="mt-1.5 text-sm text-gray-700">{{ $contrat->reconduction_auto ? __('Oui') : __('Non') }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('Conditions financières') }}</h3>

                        <div class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                            <div class="rounded-xl bg-slate-50 p-4">
                                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Loyer mensuel') }}</p>
                                <p class="mt-2 text-lg font-semibold text-gray-900"><x-money :amount="$contrat->loyer_mensuel" /></p>
                            </div>

                            <div class="rounded-xl bg-slate-50 p-4">
                                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Charges') }}</p>
                                <p class="mt-2 text-lg font-semibold text-gray-900"><x-money :amount="$contrat->charges" /></p>
                            </div>

                            <div class="rounded-xl bg-slate-50 p-4">
                                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Dépôt de garantie') }}</p>
                                <p class="mt-2 text-lg font-semibold text-gray-900"><x-money :amount="$contrat->depot_garantie" /></p>
                            </div>

                            <div class="rounded-xl bg-slate-50 p-4">
                                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Total mensuel') }}</p>
                                <p class="mt-2 text-lg font-bold text-gray-900"><x-money :amount="$contrat->montantTotalMensuel()" /></p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ __('Document du contrat') }}</h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    {{ __('Téléversement PDF du bail, conformément au périmètre retenu pour cette étape.') }}
                                </p>
                            </div>

                            @if ($contrat->documentDisponible())
                                <a
                                    href="{{ route('proprietaire.contrats.document', $contrat) }}"
                                    class="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50"
                                >
                                    {{ __('Télécharger le PDF') }} ↓
                                </a>
                            @endif
                        </div>

                        <div class="mt-5 rounded-xl border border-gray-200 bg-slate-50 p-4 text-sm text-gray-700">
                            @if ($contrat->documentDisponible())
                                <p class="font-semibold text-gray-900">{{ $contrat->nomDocument() }}</p>
                            @else
                                <p class="text-gray-400">{{ __('Aucun document n\'est encore associé à ce contrat.') }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ __('Historique des quittances') }}</h3>
                                <p class="mt-1 text-sm text-gray-500">{{ __('Quittances PDF déjà générées pour ce contrat.') }}</p>
                            </div>

                            <a
                                href="{{ route('proprietaire.quittances.index', ['contrat_id' => $contrat->id]) }}"
                                class="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50"
                            >
                                {{ __('Voir toutes les quittances') }}
                            </a>
                        </div>

                        @if ($contrat->quittances->isEmpty())
                            <p class="mt-5 text-sm text-gray-400">{{ __('Aucune quittance n\'a encore été générée pour ce contrat.') }}</p>
                        @else
                            <div class="mt-5 overflow-hidden rounded-xl border border-gray-200">
                                <table class="min-w-full divide-y divide-gray-100 text-sm">
                                    <tbody class="divide-y divide-gray-100 bg-white">
                                        @foreach ($contrat->quittances->sortByDesc('emise_le') as $quittance)
                                            <tr class="flex items-center justify-between gap-4 px-4 py-3 transition hover:bg-gray-50">
                                                <td class="min-w-0">
                                                    <p class="font-mono text-xs font-semibold text-gray-900">{{ $quittance->numero_quittance }}</p>
                                                    <p class="mt-0.5 text-xs text-gray-500">{{ $quittance->labelPeriode() }} · <x-money :amount="$quittance->paiement->montant" /></p>
                                                </td>
                                                <td class="shrink-0">
                                                    <a
                                                        href="{{ route('proprietaire.quittances.download', $quittance) }}"
                                                        class="inline-flex items-center rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50"
                                                    >
                                                        {{ __('PDF') }} ↓
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </section>

                <aside class="grid gap-6 content-start">
                    <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('Signature') }}</h3>

                        @if ($contrat->isSigne())
                            <div class="mt-5 grid gap-4 rounded-2xl bg-emerald-50 p-5 text-sm text-emerald-900">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wider text-emerald-600">{{ __('Nom du signataire') }}</p>
                                    <p class="mt-1.5 font-semibold">{{ $contrat->signe_nom }}</p>
                                </div>

                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wider text-emerald-600">{{ __('Date de signature') }}</p>
                                    <p class="mt-1.5 font-semibold">{{ $contrat->signe_le?->translatedFormat('d F Y H:i') }}</p>
                                </div>

                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wider text-emerald-600">{{ __('Adresse IP') }}</p>
                                    <p class="mt-1.5 font-mono text-xs font-semibold">{{ $contrat->signe_ip }}</p>
                                </div>
                            </div>
                        @else
                            <div class="mt-5 rounded-xl bg-amber-50 p-5 text-sm text-amber-900">
                                <p class="font-semibold">{{ __('Aucune signature enregistrée') }}</p>
                                <p class="mt-2 text-amber-700">{{ __('Le locataire peut signer ce contrat depuis son espace dédié dès que le bail lui est présenté.') }}</p>
                            </div>
                        @endif
                    </section>

                    <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('Historique') }}</h3>

                        @if ($audits->isEmpty())
                            <p class="mt-5 text-sm text-gray-400">{{ __('Aucun historique disponible pour le moment.') }}</p>
                        @else
                            <div class="mt-5 grid gap-3">
                                @foreach ($audits as $audit)
                                    <article class="rounded-xl bg-slate-50 p-4 text-sm text-gray-700">
                                        <p class="font-semibold text-gray-900">{{ str($audit->action)->replace('_', ' ')->ucfirst() }}</p>
                                        <p class="mt-0.5 text-xs text-gray-400">{{ $audit->created_at?->translatedFormat('d/m/Y H:i') }}</p>

                                        @if (! empty($audit->details))
                                            <dl class="mt-3 grid gap-1.5">
                                                @foreach ($audit->details as $cle => $valeur)
                                                    <div class="flex items-start justify-between gap-4">
                                                        <dt class="text-xs text-gray-500">{{ str($cle)->replace('_', ' ')->ucfirst() }}</dt>
                                                        <dd class="text-right text-xs text-gray-700">{{ is_bool($valeur) ? ($valeur ? 'Oui' : 'Non') : (string) $valeur }}</dd>
                                                    </div>
                                                @endforeach
                                            </dl>
                                        @endif
                                    </article>
                                @endforeach
                            </div>
                        @endif
                    </section>
                </aside>
            </div>
        </div>
    </div>
</x-app-layout>
