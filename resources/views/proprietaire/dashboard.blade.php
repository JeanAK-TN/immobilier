<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-900">
            {{ __('Tableau de bord') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-8 px-4 sm:px-6 lg:px-8">

            {{-- En-tête de bienvenue --}}
            <div class="flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-gray-400">
                        {{ ucfirst(now()->translatedFormat('l d F Y')) }}
                    </p>
                    <h1 class="mt-1 text-2xl font-bold text-gray-900">
                        {{ __('Bonjour,') }} {{ Str::before(Auth::user()->name, ' ') }}
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ __('Voici un aperçu de votre activité locative.') }}
                    </p>
                </div>
            </div>

            @if (session('status'))
                <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3.5 text-sm font-medium text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Cartes de statistiques --}}
            <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">

                {{-- Biens --}}
                <a href="{{ route('proprietaire.biens.index') }}"
                   class="group rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition hover:shadow-md">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Biens') }}</p>
                    <p class="mt-3 text-4xl font-bold text-gray-900">{{ $biensTotal }}</p>
                    <div class="mt-3 flex items-center gap-3 text-xs text-gray-500">
                        <span class="flex items-center gap-1.5">
                            <span class="inline-block h-2 w-2 rounded-full bg-amber-400"></span>
                            {{ $biensOccupes }} occupé{{ $biensOccupes > 1 ? 's' : '' }}
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="inline-block h-2 w-2 rounded-full bg-emerald-400"></span>
                            {{ $biensTotal - $biensOccupes }} libre{{ ($biensTotal - $biensOccupes) > 1 ? 's' : '' }}
                        </span>
                    </div>
                </a>

                {{-- Contrats actifs --}}
                <a href="{{ route('proprietaire.contrats.index', ['statut' => 'actif']) }}"
                   class="group rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition hover:shadow-md">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Contrats actifs') }}</p>
                    <p class="mt-3 text-4xl font-bold text-gray-900">{{ $contratsActifs }}</p>
                    @if ($contratsEnAttente > 0)
                        <p class="mt-3 text-xs font-medium text-amber-600">
                            {{ $contratsEnAttente }} en attente de signature
                        </p>
                    @else
                        <p class="mt-3 text-xs text-gray-400">{{ __('Aucune signature en attente') }}</p>
                    @endif
                </a>

                {{-- Paiements du mois --}}
                <a href="{{ route('proprietaire.paiements.index', ['periode' => now()->format('Y-m')]) }}"
                   class="group rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition hover:shadow-md">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">
                        {{ __('Paiements') }} · {{ ucfirst(now()->translatedFormat('M')) }}
                    </p>
                    <p class="mt-3 text-4xl font-bold text-gray-900">{{ $paiementsMoisCount }}</p>
                    @if ($paiementsMoisMontant > 0)
                        <p class="mt-3 text-xs font-medium text-emerald-600">
                            <x-money :amount="$paiementsMoisMontant" /> reçus
                        </p>
                    @else
                        <p class="mt-3 text-xs text-gray-400">{{ __('Aucun paiement ce mois') }}</p>
                    @endif
                </a>

                {{-- Tickets ouverts --}}
                <a href="{{ route('proprietaire.tickets.index', ['statut' => 'ouvert']) }}"
                   class="group rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition hover:shadow-md">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Tickets ouverts') }}</p>
                    <p class="mt-3 text-4xl font-bold {{ $ticketsOuverts > 0 ? 'text-amber-600' : 'text-gray-900' }}">
                        {{ $ticketsOuverts }}
                    </p>
                    <p class="mt-3 text-xs {{ $ticketsOuverts > 0 ? 'font-medium text-amber-500' : 'text-gray-400' }}">
                        {{ $ticketsOuverts > 0 ? __('En attente de traitement') : __('Aucune demande en cours') }}
                    </p>
                </a>

            </div>

            {{-- Accès rapide + derniers contrats --}}
            <div class="grid gap-6 xl:grid-cols-[minmax(0,2fr)_minmax(260px,1fr)]">

                {{-- Derniers contrats --}}
                <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                    <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                        <h3 class="font-semibold text-gray-900">{{ __('Derniers contrats') }}</h3>
                        <a href="{{ route('proprietaire.contrats.index') }}"
                           class="text-sm text-gray-400 underline-offset-2 hover:text-gray-700 hover:underline">
                            {{ __('Voir tout') }}
                        </a>
                    </div>

                    @if ($derniersContrats->isEmpty())
                        <div class="px-6 py-10 text-center text-sm text-gray-400">
                            {{ __('Aucun contrat créé pour l\'instant.') }}
                        </div>
                    @else
                        <ul class="divide-y divide-gray-100">
                            @foreach ($derniersContrats as $contrat)
                                <li>
                                    <a href="{{ route('proprietaire.contrats.show', $contrat) }}"
                                       class="flex items-center justify-between px-6 py-4 transition hover:bg-gray-50">
                                        <div class="min-w-0">
                                            <p class="truncate font-medium text-gray-900">{{ $contrat->bien->nom }}</p>
                                            <p class="mt-0.5 truncate text-sm text-gray-500">{{ $contrat->locataire->nomComplet() }}</p>
                                        </div>
                                        <div class="ml-4 shrink-0 text-right">
                                            <span @class([
                                                'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset',
                                                'bg-emerald-50 text-emerald-700 ring-emerald-200' => $contrat->statut->value === 'actif',
                                                'bg-amber-50 text-amber-700 ring-amber-200' => $contrat->statut->value === 'en_attente',
                                                'bg-gray-100 text-gray-600 ring-gray-200' => ! in_array($contrat->statut->value, ['actif', 'en_attente']),
                                            ])>
                                                {{ $contrat->statut->label() }}
                                            </span>
                                            <p class="mt-1 text-xs text-gray-400">
                                                <x-money :amount="$contrat->montantTotalMensuel()" />/mois
                                            </p>
                                        </div>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </section>

                {{-- Actions rapides --}}
                <aside class="flex flex-col gap-2.5">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Créer') }}</p>

                    <a href="{{ route('proprietaire.biens.create') }}"
                       class="flex items-center gap-3 rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-500 text-base">+</span>
                        {{ __('Ajouter un bien') }}
                    </a>

                    <a href="{{ route('proprietaire.locataires.create') }}"
                       class="flex items-center gap-3 rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-500 text-base">+</span>
                        {{ __('Créer un locataire') }}
                    </a>

                    <a href="{{ route('proprietaire.contrats.create') }}"
                       class="flex items-center gap-3 rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-500 text-base">+</span>
                        {{ __('Nouveau contrat') }}
                    </a>

                    @if ($contratsEnAttente > 0)
                        <a href="{{ route('proprietaire.contrats.index', ['statut' => 'en_attente']) }}"
                           class="mt-2 flex items-center gap-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-700 shadow-sm transition hover:bg-amber-100">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-amber-100 text-amber-600 font-bold">!</span>
                            {{ $contratsEnAttente }} contrat{{ $contratsEnAttente > 1 ? 's' : '' }} à signer
                        </a>
                    @endif
                </aside>

            </div>
        </div>
    </div>
</x-app-layout>
