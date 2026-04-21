<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    {{ __('Modifier le locataire') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Mettez à jour les informations du locataire et le statut de son compte.') }}
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a
                    href="{{ route('proprietaire.locataires.show', $locataire) }}"
                    class="inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50"
                >
                    {{ __('Voir la fiche') }}
                </a>

                <a
                    href="{{ route('proprietaire.locataires.index') }}"
                    class="inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50"
                >
                    {{ __('Retour à la liste') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm sm:p-8">
                <form method="POST" action="{{ route('proprietaire.locataires.update', $locataire) }}" class="grid gap-8">
                    @csrf
                    @method('PUT')

                    <x-proprietaire.locataires.form-fields :locataire="$locataire" :edition="true" />

                    <div class="flex flex-wrap items-center gap-3 border-t border-gray-200 pt-6">
                        <x-primary-button>{{ __('Enregistrer les modifications') }}</x-primary-button>

                        <a
                            href="{{ route('proprietaire.locataires.show', $locataire) }}"
                            class="inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition hover:bg-gray-50"
                        >
                            {{ __('Annuler') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
