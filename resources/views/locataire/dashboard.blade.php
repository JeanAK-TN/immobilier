<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Tableau de bord — Locataire') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto grid max-w-7xl gap-6 px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            <section class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                <p class="text-lg font-medium text-gray-900">{{ __('Bienvenue, :nom !', ['nom' => Auth::user()->name]) }}</p>
                <p class="mt-2 text-sm text-gray-600">
                    {{ __('Retrouvez ici votre contrat en cours et accédez rapidement à la signature lorsqu\'elle est requise.') }}
                </p>
            </section>

            <section class="grid gap-6 xl:grid-cols-[minmax(0,2fr)_minmax(280px,1fr)]">
                <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ __('Mon contrat actuel') }}</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                {{ __('Consultez votre bail et signez-le en ligne lorsqu\'une signature est attendue.') }}
                            </p>
                        </div>

                        <a
                            href="{{ route('locataire.contrat.show') }}"
                            class="inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50"
                        >
                            {{ __('Ouvrir mon contrat') }}
                        </a>
                    </div>

                    @if ($contrat)
                        <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                            <div class="rounded-2xl bg-gray-50 p-5">
                                <p class="text-xs uppercase tracking-wide text-gray-500">{{ __('Bien') }}</p>
                                <p class="mt-2 text-base font-semibold text-gray-900">{{ $contrat->bien->nom }}</p>
                            </div>

                            <div class="rounded-2xl bg-gray-50 p-5">
                                <p class="text-xs uppercase tracking-wide text-gray-500">{{ __('Statut') }}</p>
                                <p class="mt-2 text-base font-semibold text-gray-900">{{ $contrat->statut->label() }}</p>
                            </div>

                            <div class="rounded-2xl bg-gray-50 p-5">
                                <p class="text-xs uppercase tracking-wide text-gray-500">{{ __('Signature') }}</p>
                                <p class="mt-2 text-base font-semibold text-gray-900">{{ $contrat->signatureLabel() }}</p>
                            </div>

                            <div class="rounded-2xl bg-gray-50 p-5">
                                <p class="text-xs uppercase tracking-wide text-gray-500">{{ __('Total mensuel') }}</p>
                                <p class="mt-2 text-base font-semibold text-gray-900"><x-money :amount="$contrat->montantTotalMensuel()" /></p>
                            </div>
                        </div>
                    @else
                        <div class="mt-6 rounded-2xl border border-dashed border-gray-300 bg-gray-50 px-5 py-8 text-sm text-gray-600">
                            {{ __('Aucun contrat en attente de signature ou actif n\'est disponible pour le moment.') }}
                        </div>
                    @endif
                </div>

                <aside class="rounded-3xl border border-gray-200 bg-slate-900 p-6 text-slate-100 shadow-sm">
                    <h3 class="text-lg font-semibold">{{ __('Prochaine étape') }}</h3>

                    @if ($contrat && $contrat->peutEtreSigne())
                        <p class="mt-3 text-sm text-slate-200">
                            {{ __('Votre bail attend votre validation. Ouvrez la page du contrat pour télécharger le PDF puis confirmer votre signature électronique simple.') }}
                        </p>
                    @elseif ($contrat)
                        <p class="mt-3 text-sm text-slate-200">
                            {{ __('Votre contrat est consultable à tout moment depuis votre espace locataire.') }}
                        </p>
                    @else
                        <p class="mt-3 text-sm text-slate-200">
                            {{ __('Dès qu\'un contrat vous sera attribué par votre propriétaire, il apparaîtra ici.') }}
                        </p>
                    @endif
                </aside>
            </section>
        </div>
    </div>
</x-app-layout>
