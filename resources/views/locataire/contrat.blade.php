<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    {{ __('Mon contrat') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Consultez votre bail, téléchargez le PDF et signez-le si nécessaire.') }}
                </p>
            </div>

            <a
                href="{{ route('locataire.dashboard') }}"
                class="inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50"
            >
                {{ __('Retour au tableau de bord') }}
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto grid max-w-7xl gap-6 px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            @if (! $contrat)
                <section class="rounded-3xl border border-dashed border-gray-300 bg-white px-6 py-10 text-center shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('Aucun contrat à consulter') }}</h3>
                    <p class="mx-auto mt-3 max-w-2xl text-sm text-gray-600">
                        {{ __('Votre espace ne contient pas encore de contrat en attente de signature ou actif. Dès qu\'un bail vous sera attribué, il apparaîtra ici.') }}
                    </p>
                </section>
            @else
                <div class="grid gap-6 xl:grid-cols-[minmax(0,2fr)_minmax(320px,1fr)]">
                    <section class="grid gap-6">
                        <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="rounded-full bg-slate-50 px-3 py-1 text-xs font-medium text-slate-700 ring-1 ring-inset ring-slate-200">
                                    {{ $contrat->statut->label() }}
                                </span>

                                <span class="rounded-full bg-gray-50 px-3 py-1 text-xs font-medium text-gray-700 ring-1 ring-inset ring-gray-200">
                                    {{ $contrat->signatureLabel() }}
                                </span>
                            </div>

                            <h3 class="mt-4 text-lg font-semibold text-gray-900">{{ __('Informations principales') }}</h3>

                            <dl class="mt-6 grid gap-6 md:grid-cols-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Bien') }}</dt>
                                    <dd class="mt-2 text-sm font-semibold text-gray-900">{{ $contrat->bien->nom }}</dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Adresse') }}</dt>
                                    <dd class="mt-2 text-sm text-gray-900">
                                        {{ $contrat->bien->adresse }}<br>
                                        {{ $contrat->bien->ville }}, {{ $contrat->bien->pays }}
                                    </dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Date de début') }}</dt>
                                    <dd class="mt-2 text-sm text-gray-900">{{ $contrat->date_debut->translatedFormat('d F Y') }}</dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Date de fin') }}</dt>
                                    <dd class="mt-2 text-sm text-gray-900">{{ $contrat->date_fin?->translatedFormat('d F Y') ?? __('Non définie') }}</dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Jour de paiement') }}</dt>
                                    <dd class="mt-2 text-sm text-gray-900">{{ $contrat->jour_paiement }}</dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Reconduction automatique') }}</dt>
                                    <dd class="mt-2 text-sm text-gray-900">{{ $contrat->reconduction_auto ? __('Oui') : __('Non') }}</dd>
                                </div>
                            </dl>
                        </div>

                        <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                            <h3 class="text-lg font-semibold text-gray-900">{{ __('Conditions financières') }}</h3>

                            <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                                <div class="rounded-2xl bg-gray-50 p-5">
                                    <p class="text-xs uppercase tracking-wide text-gray-500">{{ __('Loyer mensuel') }}</p>
                                    <p class="mt-2 text-xl font-semibold text-gray-900"><x-money :amount="$contrat->loyer_mensuel" /></p>
                                </div>

                                <div class="rounded-2xl bg-gray-50 p-5">
                                    <p class="text-xs uppercase tracking-wide text-gray-500">{{ __('Charges') }}</p>
                                    <p class="mt-2 text-xl font-semibold text-gray-900"><x-money :amount="$contrat->charges" /></p>
                                </div>

                                <div class="rounded-2xl bg-gray-50 p-5">
                                    <p class="text-xs uppercase tracking-wide text-gray-500">{{ __('Dépôt de garantie') }}</p>
                                    <p class="mt-2 text-xl font-semibold text-gray-900"><x-money :amount="$contrat->depot_garantie" /></p>
                                </div>

                                <div class="rounded-2xl bg-gray-50 p-5">
                                    <p class="text-xs uppercase tracking-wide text-gray-500">{{ __('Total mensuel') }}</p>
                                    <p class="mt-2 text-xl font-semibold text-gray-900"><x-money :amount="$contrat->montantTotalMensuel()" /></p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <aside class="grid gap-6">
                        <section class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                            <div class="flex flex-col gap-3">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ __('Document du bail') }}</h3>
                                    <p class="mt-1 text-sm text-gray-500">
                                        {{ __('Téléchargez le PDF transmis par votre propriétaire avant de signer.') }}
                                    </p>
                                </div>

                                @if ($contrat->documentDisponible())
                                    <a
                                        href="{{ route('locataire.contrat.document') }}"
                                        class="inline-flex items-center justify-center rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50"
                                    >
                                        {{ __('Télécharger le PDF') }}
                                    </a>
                                @endif
                            </div>

                            <div class="mt-5 rounded-2xl bg-gray-50 p-5 text-sm text-gray-700">
                                @if ($contrat->documentDisponible())
                                    <p class="font-semibold">{{ $contrat->nomDocument() }}</p>
                                @else
                                    <p>{{ __('Aucun document PDF n\'est actuellement disponible pour ce contrat.') }}</p>
                                @endif
                            </div>
                        </section>

                        <section class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                            <h3 class="text-lg font-semibold text-gray-900">{{ __('Signature électronique simple') }}</h3>

                            @if ($contrat->isSigne())
                                <div class="mt-5 grid gap-4 rounded-2xl bg-emerald-50 p-5 text-sm text-emerald-900">
                                    <div>
                                        <p class="text-xs uppercase tracking-wide text-emerald-700">{{ __('Nom du signataire') }}</p>
                                        <p class="mt-2 font-semibold">{{ $contrat->signe_nom }}</p>
                                    </div>

                                    <div>
                                        <p class="text-xs uppercase tracking-wide text-emerald-700">{{ __('Date de signature') }}</p>
                                        <p class="mt-2 font-semibold">{{ $contrat->signe_le?->translatedFormat('d F Y H:i') }}</p>
                                    </div>

                                    <div>
                                        <p class="text-xs uppercase tracking-wide text-emerald-700">{{ __('Adresse IP enregistrée') }}</p>
                                        <p class="mt-2 font-semibold">{{ $contrat->signe_ip }}</p>
                                    </div>
                                </div>
                            @elseif ($contrat->peutEtreSigne())
                                <p class="mt-3 text-sm text-gray-600">
                                    {{ __('En signant, vous confirmez avoir pris connaissance du bail transmis. La date et votre adresse IP seront enregistrées comme preuve légère.') }}
                                </p>

                                <form method="POST" action="{{ route('locataire.contrat.sign') }}" class="mt-5 grid gap-5">
                                    @csrf
                                    @method('PUT')

                                    <div>
                                        <x-input-label for="signe_nom" :value="__('Nom du signataire')" />
                                        <x-text-input
                                            id="signe_nom"
                                            name="signe_nom"
                                            type="text"
                                            class="mt-1 block w-full"
                                            :value="old('signe_nom', Auth::user()->name)"
                                            required
                                        />
                                        <x-input-error :messages="$errors->get('signe_nom')" class="mt-2" />
                                    </div>

                                    <label for="confirmation_signature" class="flex items-start gap-3 rounded-2xl border border-gray-200 bg-gray-50 p-4 text-sm text-gray-700">
                                        <input
                                            id="confirmation_signature"
                                            name="confirmation_signature"
                                            type="checkbox"
                                            value="1"
                                            class="mt-1 rounded border-gray-300 text-gray-900 shadow-sm focus:ring-gray-500"
                                            @checked(old('confirmation_signature'))
                                        >
                                        <span>{{ __('Je confirme la signature de ce contrat et j\'accepte l\'enregistrement de mon nom, de la date et de mon adresse IP.') }}</span>
                                    </label>
                                    <x-input-error :messages="$errors->get('confirmation_signature')" class="mt-2" />

                                    <x-primary-button class="justify-center">
                                        {{ __('Signer le contrat') }}
                                    </x-primary-button>
                                </form>
                            @else
                                <div class="mt-5 rounded-2xl bg-amber-50 p-5 text-sm text-amber-900">
                                    <p class="font-semibold">{{ __('Signature indisponible pour le moment') }}</p>
                                    <p class="mt-2">{{ __('Ce contrat n\'est pas encore prêt à être signé depuis votre espace locataire.') }}</p>
                                </div>
                            @endif
                        </section>
                    </aside>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
