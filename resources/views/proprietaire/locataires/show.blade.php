<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <span @class([
                        'rounded-full px-3 py-1 text-xs font-medium ring-1 ring-inset',
                        'bg-emerald-50 text-emerald-700 ring-emerald-200' => $locataire->compteActif(),
                        'bg-red-50 text-red-700 ring-red-200' => ! $locataire->compteActif(),
                    ])>
                        {{ $locataire->compteActif() ? __('Compte actif') : __('Compte inactif') }}
                    </span>

                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-700 ring-1 ring-inset ring-slate-200">
                        {{ $locataire->user->must_change_password ? __('Mot de passe à changer') : __('Mot de passe déjà changé') }}
                    </span>
                </div>

                <h2 class="mt-3 text-xl font-semibold leading-tight text-gray-900">
                    {{ $locataire->nomComplet() }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ $locataire->email }} · {{ $locataire->telephone ?: __('Téléphone non renseigné') }}
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a
                    href="{{ route('proprietaire.locataires.edit', $locataire) }}"
                    class="inline-flex items-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-700"
                >
                    {{ __('Modifier') }}
                </a>

                <a
                    href="{{ route('proprietaire.locataires.index') }}"
                    class="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50"
                >
                    {{ __('Retour à la liste') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto grid max-w-7xl gap-6 px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3.5 text-sm font-medium text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            @if (session('identifiants_locataire'))
                <section class="rounded-2xl border border-blue-200 bg-blue-50 p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-blue-950">{{ __('Identifiants temporaires à transmettre') }}</h3>
                    <p class="mt-2 text-sm text-blue-800">
                        {{ __('Conservez ces identifiants maintenant : le mot de passe temporaire ne sera plus affiché ensuite.') }}
                    </p>

                    <dl class="mt-5 grid gap-4 md:grid-cols-2">
                        <div class="rounded-xl bg-white px-4 py-4 shadow-sm">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Adresse e-mail') }}</dt>
                            <dd class="mt-1.5 text-sm font-semibold text-gray-900">{{ session('identifiants_locataire.email') }}</dd>
                        </div>

                        <div class="rounded-xl bg-white px-4 py-4 shadow-sm">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Mot de passe temporaire') }}</dt>
                            <dd class="mt-1.5 font-mono text-sm font-semibold text-gray-900">{{ session('identifiants_locataire.mot_de_passe_temporaire') }}</dd>
                        </div>
                    </dl>
                </section>
            @endif

            <div class="grid gap-6 xl:grid-cols-[minmax(0,2fr)_minmax(320px,1fr)]">
                <section class="grid gap-6 content-start">
                    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('Informations du locataire') }}</h3>

                        <dl class="mt-5 grid gap-5 md:grid-cols-2">
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Prénom') }}</dt>
                                <dd class="mt-1.5 text-sm text-gray-700">{{ $locataire->prenom }}</dd>
                            </div>

                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Nom') }}</dt>
                                <dd class="mt-1.5 text-sm text-gray-700">{{ $locataire->nom }}</dd>
                            </div>

                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Adresse e-mail') }}</dt>
                                <dd class="mt-1.5 text-sm text-gray-700">{{ $locataire->email }}</dd>
                            </div>

                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Téléphone') }}</dt>
                                <dd class="mt-1.5 text-sm text-gray-700">{{ $locataire->telephone ?: __('Non renseigné') }}</dd>
                            </div>

                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Créé le') }}</dt>
                                <dd class="mt-1.5 text-sm text-gray-700">{{ $locataire->created_at->translatedFormat('d F Y') }}</dd>
                            </div>

                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Compte utilisateur') }}</dt>
                                <dd class="mt-1.5 text-sm text-gray-700">{{ $locataire->user->name }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('Suivi locatif') }}</h3>

                        <div class="mt-5 grid gap-4 md:grid-cols-2">
                            <div class="rounded-xl bg-slate-50 p-5">
                                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Nombre de contrats') }}</p>
                                <p class="mt-2 text-3xl font-bold text-gray-900">{{ $locataire->contrats_count }}</p>
                            </div>

                            <div class="rounded-xl bg-slate-50 p-5">
                                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Contrats actifs') }}</p>
                                <p class="mt-2 text-3xl font-bold text-gray-900">{{ $locataire->contrats_actifs_count }}</p>
                            </div>
                        </div>
                    </div>
                </section>

                <aside class="grid gap-6 content-start">
                    <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('Gestion du compte') }}</h3>

                        <div class="mt-5 grid gap-4 rounded-xl bg-slate-50 p-5 text-sm">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('État du compte') }}</p>
                                <p class="mt-1.5 font-semibold text-gray-900">
                                    {{ $locataire->compteActif() ? __('Actif') : __('Inactif') }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Première connexion') }}</p>
                                <p class="mt-1.5 font-semibold text-gray-900">
                                    {{ $locataire->user->must_change_password ? __('Mot de passe encore à changer') : __('Déjà effectuée') }}
                                </p>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('proprietaire.locataires.activation', $locataire) }}" class="mt-5">
                            @csrf
                            @method('PATCH')

                            <button
                                type="submit"
                                class="inline-flex items-center rounded-lg px-4 py-2 text-sm font-semibold shadow-sm transition {{ $locataire->compteActif() ? 'bg-red-600 text-white hover:bg-red-500' : 'bg-emerald-600 text-white hover:bg-emerald-500' }}"
                                onclick="return confirm('{{ $locataire->compteActif() ? 'Confirmer la désactivation de ce compte ?' : 'Confirmer la réactivation de ce compte ?' }}')"
                            >
                                {{ $locataire->compteActif() ? __('Désactiver le compte') : __('Réactiver le compte') }}
                            </button>
                        </form>
                    </section>
                </aside>
            </div>
        </div>
    </div>
</x-app-layout>
