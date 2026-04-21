<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-900">
                    {{ __('Détail d\'un paiement simulé') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Référence : :reference', ['reference' => $paiement->reference]) }}
                </p>
            </div>

            <a
                href="{{ route('proprietaire.paiements.index') }}"
                class="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50"
            >
                {{ __('Retour à la liste') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3.5 text-sm font-medium text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            @if (session('warning'))
                <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3.5 text-sm font-medium text-amber-900">
                    {{ session('warning') }}
                </div>
            @endif

            <div class="mb-6 flex flex-wrap items-center gap-3">
                @if ($paiement->quittance)
                    <a
                        href="{{ route('proprietaire.quittances.download', $paiement->quittance) }}"
                        class="inline-flex items-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-700"
                    >
                        {{ __('Télécharger la quittance PDF') }} ↓
                    </a>
                @else
                    @can('create', [App\Models\Quittance::class, $paiement])
                        <form method="POST" action="{{ route('proprietaire.quittances.store', $paiement) }}">
                            @csrf
                            <x-primary-button>{{ __('Générer la quittance PDF') }}</x-primary-button>
                        </form>
                    @endcan
                @endif
            </div>

            <x-paiements.receipt :paiement="$paiement" />
        </div>
    </div>
</x-app-layout>
