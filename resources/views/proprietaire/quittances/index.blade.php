<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-900">{{ __('Quittances PDF') }}</h2>
                <p class="mt-1 text-sm text-gray-500">{{ __('Historique des quittances générées à partir des paiements simulés réussis.') }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto grid max-w-7xl gap-6 px-4 sm:px-6 lg:px-8">

            {{-- Filtres --}}
            <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <form
                    method="GET"
                    action="{{ route('proprietaire.quittances.index') }}"
                    x-data
                    @change="$root.submit()"
                    @input.debounce.500ms="$root.submit()"
                    class="grid gap-4 sm:grid-cols-3 sm:items-end"
                >
                    <div class="grid gap-2">
                        <x-input-label for="contrat_id" :value="__('Contrat')" />
                        <select id="contrat_id" name="contrat_id" class="rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500">
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
                        <button type="submit" class="hidden" tabindex="-1" aria-hidden="true">{{ __('Filtrer') }}</button>
                        <a
                            href="{{ route('proprietaire.quittances.index') }}"
                            class="inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition hover:bg-gray-50"
                        >
                            {{ __('Réinitialiser') }}
                        </a>
                    </div>
                </form>
            </section>

            @if ($quittances->isEmpty())
                <section class="rounded-2xl border border-dashed border-gray-300 bg-white p-12 text-center shadow-sm">
                    <p class="text-4xl text-gray-300">📄</p>
                    <h3 class="mt-4 text-lg font-semibold text-gray-900">{{ __('Aucune quittance générée') }}</h3>
                    <p class="mt-2 text-sm text-gray-500">{{ __('Générez une quittance depuis la fiche d\'un paiement simulé réussi.') }}</p>
                </section>
            @else
                <div class="overflow-hidden rounded-2xl border border-gray-200 shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Numéro') }}</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Contrat') }}</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Période') }}</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Montant') }}</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Émise le') }}</th>
                                    <th class="relative px-5 py-3"><span class="sr-only">{{ __('Actions') }}</span></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @foreach ($quittances as $quittance)
                                    <tr class="transition hover:bg-gray-50">
                                        <td class="whitespace-nowrap px-5 py-4">
                                            <span class="font-mono text-xs font-semibold text-gray-900">{{ $quittance->numero_quittance }}</span>
                                        </td>
                                        <td class="px-5 py-4">
                                            <p class="font-medium text-gray-900">{{ $quittance->contrat->bien->nom }}</p>
                                            <p class="text-xs text-gray-500">{{ $quittance->contrat->locataire->nomComplet() }}</p>
                                        </td>
                                        <td class="whitespace-nowrap px-5 py-4 text-gray-600">{{ $quittance->labelPeriode() }}</td>
                                        <td class="whitespace-nowrap px-5 py-4 font-semibold text-gray-900">
                                            <x-money :amount="$quittance->paiement->montant" />
                                        </td>
                                        <td class="whitespace-nowrap px-5 py-4 text-gray-500">
                                            {{ $quittance->emise_le?->translatedFormat('d/m/Y') }}
                                        </td>
                                        <td class="whitespace-nowrap px-5 py-4 text-right">
                                            <a
                                                href="{{ route('proprietaire.quittances.download', $quittance) }}"
                                                class="font-medium text-gray-700 hover:text-gray-900"
                                            >
                                                {{ __('PDF') }} ↓
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div>
                    {{ $quittances->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
