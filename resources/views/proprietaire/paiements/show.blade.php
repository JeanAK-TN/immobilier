<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    {{ __('Détail d\'un paiement simulé') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Référence : :reference', ['reference' => $paiement->reference]) }}
                </p>
            </div>

            <a
                href="{{ route('proprietaire.paiements.index') }}"
                class="inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50"
            >
                {{ __('Retour à la liste') }}
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <x-paiements.receipt :paiement="$paiement" />
        </div>
    </div>
</x-app-layout>
