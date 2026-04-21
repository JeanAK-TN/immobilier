<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    {{ __('Reçu de paiement simulé') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Référence : :reference', ['reference' => $paiement->reference]) }}
                </p>
            </div>

            <a
                href="{{ route('locataire.paiements.index') }}"
                class="inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50"
            >
                {{ __('Retour aux paiements') }}
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if ($paiement->quittance)
                <div class="mb-6">
                    <a
                        href="{{ route('locataire.quittances.download', $paiement->quittance) }}"
                        class="inline-flex items-center rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-700"
                    >
                        {{ __('Télécharger la quittance PDF') }}
                    </a>
                </div>
            @endif

            <x-paiements.receipt :paiement="$paiement" />
        </div>
    </div>
</x-app-layout>
