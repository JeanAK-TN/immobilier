<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-900">
            {{ __('Tableau de bord') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-8 px-4 sm:px-6 lg:px-8">

            {{-- En-tête bienvenue --}}
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">
                    {{ ucfirst(now()->translatedFormat('l d F Y')) }}
                </p>
                <h1 class="mt-1 text-2xl font-bold text-gray-900">
                    {{ __('Bonjour,') }} {{ Str::before(Auth::user()->name, ' ') }}
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    @if ($contrat)
                        {{ __('Voici un aperçu de votre logement et de vos prochaines échéances.') }}
                    @else
                        {{ __('Votre espace est prêt. Un contrat vous sera bientôt attribué.') }}
                    @endif
                </p>
            </div>

            @if (session('status'))
                <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3.5 text-sm font-medium text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            @if ($contrat)
                {{-- Cartes statistiques --}}
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">

                    {{-- Prochain paiement --}}
                    @if ($prochainPaiement)
                        <a href="{{ route('locataire.paiements.index') }}"
                           class="group rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition hover:shadow-md">
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Prochain paiement') }}</p>
                            <p class="mt-3 text-2xl font-bold text-gray-900"><x-money :amount="$prochainPaiement['montant']" /></p>
                            <div class="mt-3 flex items-center gap-2 text-xs">
                                @if ($prochainPaiement['statut'] === 'en_retard')
                                    <span class="inline-block h-2 w-2 rounded-full bg-red-500"></span>
                                    <span class="font-semibold text-red-700">
                                        {{ __('En retard depuis le') }} {{ $prochainPaiement['date']->translatedFormat('d M') }}
                                    </span>
                                @elseif ($prochainPaiement['statut'] === 'a_payer')
                                    <span class="inline-block h-2 w-2 rounded-full bg-amber-500"></span>
                                    <span class="font-semibold text-amber-700">
                                        {{ __('À régler avant le') }} {{ $prochainPaiement['date']->translatedFormat('d M') }}
                                    </span>
                                @else
                                    <span class="inline-block h-2 w-2 rounded-full bg-emerald-500"></span>
                                    <span class="font-semibold text-emerald-700">
                                        {{ __('À jour · prochain') }} {{ $prochainPaiement['date']->translatedFormat('d M') }}
                                    </span>
                                @endif
                            </div>
                        </a>
                    @endif

                    {{-- Statut contrat --}}
                    <a href="{{ route('locataire.contrat.show') }}"
                       class="group rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition hover:shadow-md">
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Mon contrat') }}</p>
                        <p class="mt-3 truncate text-base font-semibold text-gray-900">{{ $contrat->bien->nom }}</p>
                        <div class="mt-3 flex items-center gap-2 text-xs">
                            @if ($contrat->isSigne())
                                <span class="inline-block h-2 w-2 rounded-full bg-emerald-500"></span>
                                <span class="font-semibold text-emerald-700">{{ __('Signé') }}</span>
                            @elseif ($contrat->peutEtreSigne())
                                <span class="inline-block h-2 w-2 rounded-full bg-amber-500"></span>
                                <span class="font-semibold text-amber-700">{{ __('À signer') }}</span>
                            @else
                                <span class="inline-block h-2 w-2 rounded-full bg-gray-400"></span>
                                <span class="font-medium text-gray-600">{{ $contrat->statut->label() }}</span>
                            @endif
                        </div>
                    </a>

                    {{-- Tickets ouverts --}}
                    <a href="{{ route('locataire.tickets.index') }}"
                       class="group rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition hover:shadow-md">
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Tickets actifs') }}</p>
                        <p class="mt-3 text-2xl font-bold {{ $ticketsActifsCount > 0 ? 'text-amber-600' : 'text-gray-900' }}">{{ $ticketsActifsCount }}</p>
                        <p class="mt-3 text-xs {{ $ticketsActifsCount > 0 ? 'font-medium text-amber-600' : 'text-gray-400' }}">
                            {{ $ticketsActifsCount > 0 ? __('En cours de traitement') : __('Aucun incident en cours') }}
                        </p>
                    </a>

                    {{-- Dernière quittance --}}
                    <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Dernière quittance') }}</p>
                        @if ($derniereQuittance)
                            <p class="mt-3 text-base font-semibold text-gray-900">{{ ucfirst($derniereQuittance->labelPeriode()) }}</p>
                            <a href="{{ route('locataire.quittances.download', $derniereQuittance) }}"
                               class="mt-3 inline-flex items-center gap-1 text-xs font-semibold text-gray-700 transition hover:text-gray-900">
                                {{ __('Télécharger le PDF') }} ↓
                            </a>
                        @else
                            <p class="mt-3 text-base font-semibold text-gray-400">{{ __('Aucune') }}</p>
                            <p class="mt-3 text-xs text-gray-400">{{ __('Apparaîtra après un paiement') }}</p>
                        @endif
                    </div>
                </div>

                {{-- 2 colonnes : synthèse + CTA --}}
                <div class="grid gap-6 xl:grid-cols-[minmax(0,2fr)_minmax(280px,1fr)]">

                    {{-- Synthèse logement --}}
                    <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">{{ __('Mon logement') }}</h3>
                            <a href="{{ route('locataire.contrat.show') }}"
                               class="text-sm text-gray-400 underline-offset-2 hover:text-gray-700 hover:underline">
                                {{ __('Voir le contrat') }}
                            </a>
                        </div>

                        <div class="mt-5 grid gap-4 sm:grid-cols-2">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Bien') }}</p>
                                <p class="mt-1 text-sm font-semibold text-gray-900">{{ $contrat->bien->nom }}</p>
                                <p class="text-xs text-gray-500">{{ $contrat->bien->adresse }}, {{ $contrat->bien->ville }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Loyer mensuel') }}</p>
                                <p class="mt-1 text-sm font-semibold text-gray-900"><x-money :amount="$contrat->montantTotalMensuel()" /></p>
                                <p class="text-xs text-gray-500">{{ __('Loyer + charges') }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Période') }}</p>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ __('Depuis le :date', ['date' => $contrat->date_debut->translatedFormat('d/m/Y')]) }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    {{ $contrat->date_fin
                                        ? __('Fin le :date', ['date' => $contrat->date_fin->translatedFormat('d/m/Y')])
                                        : __('Sans date de fin') }}
                                </p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Jour de paiement') }}</p>
                                <p class="mt-1 text-sm text-gray-900">{{ __('Le :j de chaque mois', ['j' => $contrat->jour_paiement]) }}</p>
                            </div>
                        </div>
                    </section>

                    {{-- Prochaine étape --}}
                    <aside class="rounded-2xl border border-gray-200 bg-gray-900 p-6 text-slate-100 shadow-sm">
                        <h3 class="text-sm font-semibold uppercase tracking-wider text-slate-400">{{ __('Prochaine étape') }}</h3>

                        @if ($contrat->peutEtreSigne())
                            <p class="mt-4 text-sm leading-relaxed text-slate-200">
                                {{ __('Votre bail attend votre signature. Téléchargez le PDF puis confirmez votre signature électronique simple.') }}
                            </p>
                            <a href="{{ route('locataire.contrat.show') }}"
                               class="mt-5 inline-flex items-center rounded-lg bg-white px-4 py-2 text-sm font-semibold text-gray-900 shadow-sm transition hover:bg-slate-100">
                                {{ __('Signer maintenant') }}
                            </a>
                        @elseif ($prochainPaiement && $prochainPaiement['statut'] === 'en_retard')
                            <p class="mt-4 text-sm leading-relaxed text-slate-200">
                                {{ __('Votre paiement de :periode est en retard. Régularisez dès que possible.', ['periode' => ucfirst(now()->translatedFormat('F Y'))]) }}
                            </p>
                            <a href="{{ route('locataire.paiements.index') }}"
                               class="mt-5 inline-flex items-center rounded-lg bg-white px-4 py-2 text-sm font-semibold text-gray-900 shadow-sm transition hover:bg-slate-100">
                                {{ __('Régler maintenant') }}
                            </a>
                        @elseif ($prochainPaiement && $prochainPaiement['statut'] === 'a_payer')
                            <p class="mt-4 text-sm leading-relaxed text-slate-200">
                                {{ __('Votre paiement de :periode est à régler avant le :date.', [
                                    'periode' => ucfirst(now()->translatedFormat('F Y')),
                                    'date' => $prochainPaiement['date']->translatedFormat('d F'),
                                ]) }}
                            </p>
                            <a href="{{ route('locataire.paiements.index') }}"
                               class="mt-5 inline-flex items-center rounded-lg bg-white px-4 py-2 text-sm font-semibold text-gray-900 shadow-sm transition hover:bg-slate-100">
                                {{ __('Faire un paiement') }}
                            </a>
                        @else
                            <p class="mt-4 text-sm leading-relaxed text-slate-200">
                                {{ __('Vous êtes à jour. Profitez de votre logement.') }}
                            </p>
                            <a href="{{ route('locataire.tickets.index') }}"
                               class="mt-5 inline-flex items-center rounded-lg border border-slate-700 px-4 py-2 text-sm font-semibold text-slate-100 transition hover:bg-slate-800">
                                {{ __('Signaler un incident') }}
                            </a>
                        @endif
                    </aside>
                </div>
            @else
                {{-- Pas de contrat --}}
                <section class="rounded-2xl border border-dashed border-gray-300 bg-white p-12 text-center shadow-sm">
                    <p class="text-4xl text-gray-300">📋</p>
                    <h3 class="mt-4 text-lg font-semibold text-gray-900">{{ __('Aucun contrat pour l\'instant') }}</h3>
                    <p class="mt-2 text-sm text-gray-500">
                        {{ __('Dès qu\'un bail vous sera attribué par votre propriétaire, il apparaîtra ici.') }}
                    </p>
                </section>
            @endif
        </div>
    </div>
</x-app-layout>
