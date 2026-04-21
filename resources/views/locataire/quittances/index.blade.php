<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-900">{{ __('Mes quittances PDF') }}</h2>
                <p class="mt-1 text-sm text-gray-500">{{ __('Téléchargez les quittances mises à disposition après un paiement simulé réussi.') }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto grid max-w-7xl gap-6 px-4 sm:px-6 lg:px-8">

            @if ($quittances->isEmpty())
                <section class="rounded-2xl border border-dashed border-gray-300 bg-white p-12 text-center shadow-sm">
                    <p class="text-4xl text-gray-300">📄</p>
                    <h3 class="mt-4 text-lg font-semibold text-gray-900">{{ __('Aucune quittance disponible') }}</h3>
                    <p class="mt-2 text-sm text-gray-500">{{ __('Une quittance apparaîtra ici dès que votre propriétaire en générera une pour l\'une de vos périodes réglées.') }}</p>
                </section>
            @else
                <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Numéro') }}</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Bien') }}</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Période') }}</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Montant') }}</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Émise le') }}</th>
                                    <th class="relative px-5 py-3"><span class="sr-only">{{ __('Télécharger') }}</span></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @foreach ($quittances as $quittance)
                                    <tr class="transition hover:bg-gray-50">
                                        <td class="whitespace-nowrap px-5 py-4">
                                            <span class="font-mono text-xs font-semibold text-gray-900">{{ $quittance->numero_quittance }}</span>
                                        </td>
                                        <td class="whitespace-nowrap px-5 py-4 text-gray-700">{{ $quittance->contrat->bien->nom }}</td>
                                        <td class="whitespace-nowrap px-5 py-4 text-gray-600">{{ $quittance->labelPeriode() }}</td>
                                        <td class="whitespace-nowrap px-5 py-4 font-semibold text-gray-900">
                                            <x-money :amount="$quittance->paiement->montant" />
                                        </td>
                                        <td class="whitespace-nowrap px-5 py-4 text-gray-500">
                                            {{ $quittance->emise_le?->translatedFormat('d/m/Y') }}
                                        </td>
                                        <td class="whitespace-nowrap px-5 py-4 text-right">
                                            <a
                                                href="{{ route('locataire.quittances.download', $quittance) }}"
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
