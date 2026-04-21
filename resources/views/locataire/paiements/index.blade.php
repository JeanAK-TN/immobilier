<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-900">
                    {{ __('Paiements') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Enregistrez un paiement pour votre contrat actif et récupérez immédiatement votre reçu.') }}
                </p>
            </div>
        </div>
    </x-slot>

    <div
        class="py-8"
        x-data="{ formOpen: {{ ($contrat && $paiements && $paiements->isEmpty()) ? 'true' : 'false' }} }"
    >
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3.5 text-sm font-medium text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            @if (session('warning'))
                <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3.5 text-sm font-medium text-amber-800">
                    {{ session('warning') }}
                </div>
            @endif

            <div class="flex items-start gap-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3.5 text-sm text-amber-900">
                <span class="mt-0.5 shrink-0 text-amber-500">⚠</span>
                <div>
                    <p class="font-semibold">{{ __('Paiement simulé - aucune transaction réelle') }}</p>
                    <p class="mt-1 text-amber-800">{{ __('Toutes les opérations de cette page sont fictives et sont automatiquement validées comme réussies pour les besoins du projet.') }}</p>
                </div>
            </div>

            @if (! $contrat)
                <section class="rounded-2xl border border-dashed border-gray-300 bg-white px-6 py-12 text-center shadow-sm">
                    <p class="text-4xl text-gray-300">💳</p>
                    <h3 class="mt-4 text-lg font-semibold text-gray-900">{{ __('Aucun contrat actif') }}</h3>
                    <p class="mt-3 text-sm text-gray-600">
                        {{ __('Vous devez disposer d\'un contrat actif pour enregistrer un paiement.') }}
                    </p>
                </section>
            @else

                {{-- Barre d'actions --}}
                <div class="flex items-center justify-between gap-4">
                    <p class="text-sm font-semibold uppercase tracking-wider text-gray-500">
                        {{ __('Historique') }}
                        @if ($paiements && $paiements->total() > 0)
                            <span class="ml-2 rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-600">{{ $paiements->total() }}</span>
                        @endif
                    </p>
                    <button
                        type="button"
                        @click="formOpen = !formOpen"
                        class="inline-flex items-center gap-2 rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-700"
                    >
                        <span x-text="formOpen ? '✕ Annuler' : '+ Nouveau paiement'"></span>
                    </button>
                </div>

                {{-- Formulaire collapsible --}}
                <div x-show="formOpen" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                    <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                        <div class="mb-5">
                            <h3 class="text-lg font-semibold text-gray-900">{{ __('Nouveau paiement') }}</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                {{ __('Contrat actif pour :bien. Le montant proposé peut être ajusté avant validation.', ['bien' => $contrat->bien->nom]) }}
                            </p>
                        </div>

                        <form
                            method="POST"
                            action="{{ route('locataire.paiements.store') }}"
                            class="grid gap-5"
                            x-data="{ mode: '{{ old('mode', $modeOptions[0]->value) }}' }"
                        >
                            @csrf

                            <div class="grid gap-5 sm:grid-cols-2">
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

                            <div class="flex flex-wrap items-center gap-3 border-t border-gray-100 pt-4">
                                <x-primary-button>{{ __('Valider le paiement') }}</x-primary-button>
                                <button type="button" @click="$root.formOpen = false" class="text-sm text-gray-500 hover:text-gray-700">
                                    {{ __('Annuler') }}
                                </button>
                            </div>
                        </form>
                    </section>
                </div>

                {{-- Contenu principal : infos contrat + historique --}}
                <div class="grid gap-6 xl:grid-cols-[minmax(240px,0.28fr)_minmax(0,1fr)]">

                    {{-- Sidebar : infos contrat --}}
                    <aside class="grid gap-4 content-start">
                        <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Contrat actif') }}</p>
                            <p class="mt-2 font-semibold text-gray-900">{{ $contrat->bien->nom }}</p>
                            <p class="mt-0.5 text-xs text-gray-500">{{ $contrat->bien->adresse }}, {{ $contrat->bien->ville }}</p>

                            <dl class="mt-4 grid gap-3 text-sm">
                                <div>
                                    <dt class="text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('Périodicité') }}</dt>
                                    <dd class="mt-1 text-gray-700">{{ __('Mensuel, autour du :jour', ['jour' => $contrat->jour_paiement]) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('Montant conseillé') }}</dt>
                                    <dd class="mt-1 font-semibold text-gray-900"><x-money :amount="$contrat->montantTotalMensuel()" /></dd>
                                </div>
                            </dl>
                        </section>
                    </aside>

                    {{-- Historique des paiements --}}
                    <section class="rounded-2xl border border-gray-200 bg-white shadow-sm">
                        @if ($paiements && $paiements->isNotEmpty())
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-100 text-sm">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Période') }}</th>
                                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Montant') }}</th>
                                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Mode') }}</th>
                                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Référence') }}</th>
                                            <th class="relative px-5 py-3"><span class="sr-only">{{ __('Actions') }}</span></th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 bg-white">
                                        @foreach ($paiements as $paiement)
                                            <tr class="transition hover:bg-gray-50">
                                                <td class="whitespace-nowrap px-5 py-4 font-medium text-gray-900">{{ $paiement->labelPeriode() }}</td>
                                                <td class="whitespace-nowrap px-5 py-4 font-semibold text-gray-900">
                                                    <x-money :amount="$paiement->montant" />
                                                </td>
                                                <td class="whitespace-nowrap px-5 py-4 text-gray-600">{{ $paiement->modeLabel() }}</td>
                                                <td class="whitespace-nowrap px-5 py-4 font-mono text-xs text-gray-400">{{ $paiement->reference }}</td>
                                                <td class="whitespace-nowrap px-5 py-4 text-right">
                                                    <a
                                                        href="{{ route('locataire.paiements.show', $paiement) }}"
                                                        class="font-medium text-gray-700 hover:text-gray-900"
                                                    >
                                                        {{ __('Reçu') }} →
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="px-6 py-4">
                                {{ $paiements->links() }}
                            </div>
                        @else
                            <div class="px-6 py-10 text-center text-sm text-gray-400">
                                {{ __('Aucun paiement enregistré pour le moment.') }}
                            </div>
                        @endif
                    </section>
                </div>

            @endif
        </div>
    </div>
</x-app-layout>
