<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-900">
                    {{ __('Paiements simulés') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Suivez toutes les simulations de paiement de vos locataires, par bien, période et statut.') }}
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto grid max-w-7xl gap-6 px-4 sm:px-6 lg:px-8">

            <div class="flex items-start gap-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3.5 text-sm text-amber-900">
                <span class="mt-0.5 shrink-0 text-amber-500">⚠</span>
                <div>
                    <p class="font-semibold">{{ __('Paiement simulé - aucune transaction réelle') }}</p>
                    <p class="mt-1 text-amber-800">{{ __('Tous les paiements affichés ici sont des simulations automatiquement validées comme réussies lors de leur création.') }}</p>
                </div>
            </div>

            {{-- Filtres --}}
            <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <form
                    method="GET"
                    action="{{ route('proprietaire.paiements.index') }}"
                    x-data
                    @change="$root.submit()"
                    @input.debounce.500ms="$root.submit()"
                    class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 lg:items-end"
                >
                    <div class="grid gap-2">
                        <x-input-label for="bien_id" :value="__('Bien')" />
                        <select id="bien_id" name="bien_id" class="rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500">
                            <option value="">{{ __('Tous les biens') }}</option>
                            @foreach ($biens as $bien)
                                <option value="{{ $bien->id }}" @selected($filtres['bienId'] === (string) $bien->id)>{{ $bien->nom }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid gap-2">
                        <x-input-label for="locataire_id" :value="__('Locataire')" />
                        <select id="locataire_id" name="locataire_id" class="rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500">
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
                        <select id="statut" name="statut" class="rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500">
                            <option value="">{{ __('Tous les statuts') }}</option>
                            @foreach ($statutOptions as $statutOption)
                                <option value="{{ $statutOption->value }}" @selected($filtres['statut'] === $statutOption->value)>{{ $statutOption->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex flex-wrap items-center gap-3 sm:col-span-2 lg:col-span-4">
                        <button type="submit" class="hidden" tabindex="-1" aria-hidden="true">{{ __('Filtrer') }}</button>
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
                <section class="rounded-2xl border border-dashed border-gray-300 bg-white p-12 text-center shadow-sm">
                    <p class="text-4xl text-gray-300">💳</p>
                    <h3 class="mt-4 text-lg font-semibold text-gray-900">{{ __('Aucun paiement trouvé') }}</h3>
                    <p class="mt-2 text-sm text-gray-500">{{ __('Ajustez les filtres ou attendez qu\'un locataire enregistre une nouvelle simulation.') }}</p>
                </section>
            @else
                <div class="overflow-hidden rounded-2xl border border-gray-200 shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Locataire') }}</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Bien') }}</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Période') }}</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Montant') }}</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Mode') }}</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Référence') }}</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Statut') }}</th>
                                    <th class="relative px-5 py-3"><span class="sr-only">{{ __('Actions') }}</span></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @foreach ($paiements as $paiement)
                                    <tr class="transition hover:bg-gray-50">
                                        <td class="whitespace-nowrap px-5 py-4">
                                            <p class="font-medium text-gray-900">{{ $paiement->contrat->locataire->nomComplet() }}</p>
                                        </td>
                                        <td class="whitespace-nowrap px-5 py-4 text-gray-600">{{ $paiement->contrat->bien->nom }}</td>
                                        <td class="whitespace-nowrap px-5 py-4 text-gray-600">{{ $paiement->labelPeriode() }}</td>
                                        <td class="whitespace-nowrap px-5 py-4 font-semibold text-gray-900">
                                            <x-money :amount="$paiement->montant" />
                                        </td>
                                        <td class="whitespace-nowrap px-5 py-4 text-gray-600">{{ $paiement->modeLabel() }}</td>
                                        <td class="whitespace-nowrap px-5 py-4 font-mono text-xs text-gray-400">{{ $paiement->reference }}</td>
                                        <td class="whitespace-nowrap px-5 py-4">
                                            <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-200">
                                                {{ $paiement->statut->label() }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-5 py-4 text-right">
                                            <a
                                                href="{{ route('proprietaire.paiements.show', $paiement) }}"
                                                class="font-medium text-gray-700 hover:text-gray-900"
                                            >
                                                {{ __('Voir') }} →
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div>
                    {{ $paiements->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
