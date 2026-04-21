<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tableau de bord — Propriétaire') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <p class="text-lg font-medium">Bienvenue, {{ Auth::user()->name }} !</p>
                    <p class="mt-2 text-sm text-gray-600">
                        Votre espace propriétaire évolue. Vous pouvez déjà gérer vos biens, vos locataires et les contrats associés.
                    </p>

                    <div class="mt-6 flex flex-wrap gap-3">
                        <a
                            href="{{ route('proprietaire.biens.index') }}"
                            class="inline-flex items-center rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-700"
                        >
                            Gérer mes biens
                        </a>

                        <a
                            href="{{ route('proprietaire.biens.create') }}"
                            class="inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50"
                        >
                            Ajouter un bien
                        </a>

                        <a
                            href="{{ route('proprietaire.locataires.index') }}"
                            class="inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50"
                        >
                            Gérer mes locataires
                        </a>

                        <a
                            href="{{ route('proprietaire.contrats.index') }}"
                            class="inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50"
                        >
                            Gérer mes contrats
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
