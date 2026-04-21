<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    {{ __('Paiements') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Enregistrez un paiement pour votre contrat actif et récupérez immédiatement votre reçu.') }}
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto grid max-w-7xl gap-6 px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            @if (session('warning'))
                <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                    {{ session('warning') }}
                </div>
            @endif

            <div class="rounded-3xl border border-amber-200 bg-amber-50 p-5 text-sm text-amber-900">
                <p class="font-semibold">{{ __('Paiement simulé - aucune transaction réelle') }}</p>
                <p class="mt-2">{{ __('Toutes les opérations de cette page sont fictives et sont automatiquement validées comme réussies pour les besoins du projet.') }}</p>
            </div>

            @if (! $contrat)
                <section class="rounded-3xl border border-dashed border-gray-300 bg-white px-6 py-10 text-center shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('Aucun contrat actif') }}</h3>
                    <p class="mt-3 text-sm text-gray-600">
                        {{ __('Vous devez disposer d\'un contrat actif pour enregistrer un paiement.') }}
                    </p>
                </section>
            @else
                <div class="grid gap-6 xl:grid-cols-[minmax(0,1.3fr)_minmax(360px,1fr)]">
                    <section class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                        <div class="flex flex-col gap-2">
                            <h3 class="text-lg font-semibold text-gray-900">{{ __('Nouveau paiement') }}</h3>
                            <p class="text-sm text-gray-500">
                                {{ __('Contrat actif pour :bien. Le montant proposé peut être ajusté avant validation.', ['bien' => $contrat->bien->nom]) }}
                            </p>
                        </div>

                        <form
                            method="POST"
                            action="{{ route('locataire.paiements.store') }}"
                            class="mt-6 grid gap-5"
                            x-data="{ mode: '{{ old('mode', $modeOptions[0]->value) }}' }"
                        >
                            @csrf

                            <div class="grid gap-5 md:grid-cols-2">
                                <div>
                                    <x-input-label for="periode" :value="__('Période concernée')" />
                                    <x-text-input
                                        id="periode"
                                        name="periode"
                                        type="month"
                                        class="mt-1 block w-full"
                                        :value="old('periode', now()->format('Y-m'))"
                                        required
                                    />
                                    <x-input-error :messages="$errors->get('periode')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="montant" :value="__('Montant (FCFA)')" />
                                    <x-text-input
                                        id="montant"
                                        name="montant"
                                        type="number"
                                        min="1"
                                        step="1"
                                        class="mt-1 block w-full"
                                        :value="old('montant', (int) round($contrat->montantTotalMensuel()))"
                                        required
                                    />
                                    <x-input-error :messages="$errors->get('montant')" class="mt-2" />
                                </div>
                            </div>

                            <div class="grid gap-5 md:grid-cols-2">
                                <div>
                                    <x-input-label for="mode" :value="__('Mode de paiement')" />
                                    <select
                                        id="mode"
                                        name="mode"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        x-model="mode"
                                    >
                                        @foreach ($modeOptions as $modeOption)
                                            <option value="{{ $modeOption->value }}">{{ $modeOption->label() }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('mode')" class="mt-2" />
                                </div>

                                <div x-show="mode === 'mobile_money'" x-cloak>
                                    <x-input-label for="operateur_mobile_money" :value="__('Opérateur Mobile Money')" />
                                    <select
                                        id="operateur_mobile_money"
                                        name="operateur_mobile_money"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                        <option value="">{{ __('Choisir un opérateur') }}</option>
                                        @foreach ($operateurOptions as $operateurOption)
                                            <option value="{{ $operateurOption->value }}" @selected(old('operateur_mobile_money') === $operateurOption->value)>
                                                {{ $operateurOption->label() }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('operateur_mobile_money')" class="mt-2" />
                                </div>
                            </div>

                            <div class="rounded-2xl bg-gray-50 p-4 text-sm text-gray-700">
                                <p class="font-semibold text-gray-900">{{ __('Règle de simulation') }}</p>
                                <p class="mt-2">{{ __('Cette application enregistre immédiatement le paiement comme “Simulé - Réussi”. Aucun traitement bancaire ou Mobile Money réel n\'est exécuté.') }}</p>
                            </div>

                            <x-primary-button class="justify-center">
                                {{ __('Valider le paiement') }}
                            </x-primary-button>
                        </form>
                    </section>

                    <aside class="grid gap-6">
                        <section class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                            <h3 class="text-lg font-semibold text-gray-900">{{ __('Contrat utilisé') }}</h3>

                            <dl class="mt-5 grid gap-4 text-sm text-gray-700">
                                <div>
                                    <dt class="text-xs uppercase tracking-wide text-gray-500">{{ __('Bien') }}</dt>
                                    <dd class="mt-2 font-semibold text-gray-900">{{ $contrat->bien->nom }}</dd>
                                </div>

                                <div>
                                    <dt class="text-xs uppercase tracking-wide text-gray-500">{{ __('Périodicité') }}</dt>
                                    <dd class="mt-2 font-semibold text-gray-900">{{ __('Mensuel, paiement attendu autour du :jour', ['jour' => $contrat->jour_paiement]) }}</dd>
                                </div>

                                <div>
                                    <dt class="text-xs uppercase tracking-wide text-gray-500">{{ __('Montant conseillé') }}</dt>
                                    <dd class="mt-2 font-semibold text-gray-900"><x-money :amount="$contrat->montantTotalMensuel()" /></dd>
                                </div>
                            </dl>
                        </section>
                    </aside>
                </div>

                <section class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ __('Historique des simulations') }}</h3>
                            <p class="mt-1 text-sm text-gray-500">{{ __('Touts vos paiements liées au contrat actif.') }}</p>
                        </div>
                    </div>

                    @if ($paiements && $paiements->isNotEmpty())
                        <div class="mt-6 grid gap-4">
                            @foreach ($paiements as $paiement)
                                <article class="grid gap-4 rounded-2xl border border-gray-200 p-5 md:grid-cols-[1.2fr_1fr_auto] md:items-center">
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $paiement->labelPeriode() }}</p>
                                        <p class="mt-1 text-sm text-gray-500">{{ $paiement->reference }}</p>
                                    </div>

                                    <div class="grid gap-1 text-sm text-gray-600">
                                        <p><x-money :amount="$paiement->montant" /></p>
                                        <p>{{ $paiement->modeLabel() }}</p>
                                    </div>

                                    <a
                                        href="{{ route('locataire.paiements.show', $paiement) }}"
                                        class="inline-flex items-center justify-center rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50"
                                    >
                                        {{ __('Voir le reçu') }}
                                    </a>
                                </article>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            {{ $paiements->links() }}
                        </div>
                    @else
                        <p class="mt-6 text-sm text-gray-500">{{ __('Aucun paiement enregistré pour le moment.') }}</p>
                    @endif
                </section>
            @endif
        </div>
    </div>
</x-app-layout>
