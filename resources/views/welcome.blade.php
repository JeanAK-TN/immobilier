<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'ImmoPro') }} · Gestion immobilière</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .dot-grid {
            background-image: radial-gradient(circle, #cbd5e1 1px, transparent 1px);
            background-size: 24px 24px;
        }
    </style>
</head>
<body class="font-sans antialiased bg-slate-50 text-gray-900">

    {{-- ── Navigation ───────────────────────────────────────────────────────── --}}
    <nav class="fixed inset-x-0 top-0 z-50 border-b border-slate-200 bg-white/95 backdrop-blur-sm">
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-600">
                    <svg class="h-4.5 w-4.5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </span>
                <span class="text-base font-semibold text-gray-900">{{ config('app.name', 'ImmoPro') }}</span>
            </a>

            <div class="flex items-center gap-2">
                <a href="{{ route('login') }}" class="rounded-lg px-4 py-2 text-sm font-medium text-gray-600 transition hover:bg-slate-100 hover:text-gray-900">
                    Connexion
                </a>
                <a href="{{ route('register') }}" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700">
                    Créer un compte
                </a>
            </div>
        </div>
    </nav>

    {{-- ── Héros ────────────────────────────────────────────────────────────── --}}
    <section class="relative overflow-hidden pt-28 pb-16 sm:pt-36 sm:pb-24">
        <div class="dot-grid absolute inset-0 opacity-60"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-transparent via-slate-50/60 to-slate-50"></div>

        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid items-center gap-12 lg:grid-cols-2 lg:gap-16">

                {{-- Texte gauche --}}
                <div>
                    <p class="mb-4 text-xs font-semibold uppercase tracking-widest text-blue-600">Gestion immobilière</p>
                    <h1 class="mb-6 text-4xl font-extrabold leading-tight text-gray-900 sm:text-5xl">
                        Gérez vos biens,<br>
                        <span class="text-blue-600">simplement.</span>
                    </h1>
                    <p class="mb-8 max-w-md text-lg text-gray-500 leading-relaxed">
                        Pour gérer vos locations sans vous perdre dans des fichiers Excel ou des carnets. Les baux, les paiements, les quittances et les demandes de maintenance, au même endroit.
                    </p>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('register') }}" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">
                            Commencer
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </a>
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-6 py-3 text-sm font-medium text-gray-700 transition hover:bg-slate-50">
                            J'ai déjà un compte
                        </a>
                    </div>
                </div>

                {{-- Aperçu tableau de bord (droite) --}}
                <div class="hidden lg:block">
                    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg">
                        <div class="mb-5 flex items-center justify-between">
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Tableau de bord</p>
                            <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-semibold text-emerald-700">En ligne</span>
                        </div>

                        {{-- Stats mini --}}
                        <div class="mb-5 grid grid-cols-2 gap-3">
                            <div class="rounded-xl border border-gray-100 bg-slate-50 p-4">
                                <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">Biens</p>
                                <p class="mt-1.5 text-3xl font-bold text-gray-900">4</p>
                                <p class="mt-1 text-[11px] text-gray-400"><span class="text-amber-500 font-medium">3 occupés</span> · 1 libre</p>
                            </div>
                            <div class="rounded-xl border border-gray-100 bg-slate-50 p-4">
                                <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">Contrats</p>
                                <p class="mt-1.5 text-3xl font-bold text-gray-900">3</p>
                                <p class="mt-1 text-[11px] text-gray-400"><span class="text-emerald-600 font-medium">actifs</span></p>
                            </div>
                            <div class="rounded-xl border border-gray-100 bg-slate-50 p-4">
                                <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">Paiements · Avr</p>
                                <p class="mt-1.5 text-3xl font-bold text-gray-900">5</p>
                                <p class="mt-1 text-[11px] font-medium text-emerald-600">750 000 FCFA reçus</p>
                            </div>
                            <div class="rounded-xl border border-gray-100 bg-slate-50 p-4">
                                <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">Tickets</p>
                                <p class="mt-1.5 text-3xl font-bold text-gray-900">1</p>
                                <p class="mt-1 text-[11px] font-medium text-amber-500">En attente</p>
                            </div>
                        </div>

                        {{-- Liste mini contrats --}}
                        <div class="rounded-xl border border-gray-100">
                            <div class="border-b border-gray-100 px-4 py-3">
                                <p class="text-xs font-semibold text-gray-500">Derniers contrats</p>
                            </div>
                            <ul class="divide-y divide-gray-50">
                                <li class="flex items-center justify-between px-4 py-3 text-xs">
                                    <div>
                                        <p class="font-medium text-gray-800">Appt. Tokoin</p>
                                        <p class="text-gray-400">Kofi Mensah</p>
                                    </div>
                                    <span class="rounded-full bg-emerald-50 px-2.5 py-1 font-semibold text-emerald-700">Actif</span>
                                </li>
                                <li class="flex items-center justify-between px-4 py-3 text-xs">
                                    <div>
                                        <p class="font-medium text-gray-800">Villa Bè</p>
                                        <p class="text-gray-400">Akossiwa Adzoa</p>
                                    </div>
                                    <span class="rounded-full bg-amber-50 px-2.5 py-1 font-semibold text-amber-700">En attente</span>
                                </li>
                                <li class="flex items-center justify-between px-4 py-3 text-xs">
                                    <div>
                                        <p class="font-medium text-gray-800">Bureau Adidogomé</p>
                                        <p class="text-gray-400">Yao Koffi</p>
                                    </div>
                                    <span class="rounded-full bg-emerald-50 px-2.5 py-1 font-semibold text-emerald-700">Actif</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ── Comment ça marche ────────────────────────────────────────────────── --}}
    <section class="border-y border-slate-200 bg-white py-16 px-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-5xl">
            <p class="mb-10 text-center text-xs font-semibold uppercase tracking-widest text-gray-400">Comment ça marche</p>

            <div class="grid gap-8 sm:grid-cols-3">
                <div class="relative text-center sm:text-left">
                    <p class="mb-3 text-5xl font-black text-slate-100">01</p>
                    <h3 class="mb-2 font-semibold text-gray-900">Le propriétaire configure</h3>
                    <p class="text-sm leading-relaxed text-gray-500">Il enregistre ses biens, crée les comptes de ses locataires et prépare les contrats dans l'application.</p>
                </div>

                <div class="relative text-center sm:text-left">
                    <p class="mb-3 text-5xl font-black text-slate-100">02</p>
                    <h3 class="mb-2 font-semibold text-gray-900">Le locataire se connecte</h3>
                    <p class="text-sm leading-relaxed text-gray-500">Il reçoit ses identifiants par email, se connecte et peut signer son bail directement depuis son compte.</p>
                </div>

                <div class="relative text-center sm:text-left">
                    <p class="mb-3 text-5xl font-black text-slate-100">03</p>
                    <h3 class="mb-2 font-semibold text-gray-900">Tout reste à jour</h3>
                    <p class="text-sm leading-relaxed text-gray-500">Les paiements, les quittances et les tickets s'accumulent au fil du temps. Rien ne se perd.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ── Fonctionnalités ─────────────────────────────────────────────────── --}}
    <section class="py-16 px-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-5xl">
            <div class="mb-12">
                <p class="mb-3 text-xs font-semibold uppercase tracking-widest text-gray-400">Fonctionnalités</p>
                <h2 class="text-2xl font-bold text-gray-900">Ce que vous obtenez</h2>
            </div>

            <div class="divide-y divide-slate-100">
                @foreach ([
                    ['label' => 'Gestion des biens', 'desc' => 'Ajoutez vos appartements, maisons, bureaux ou locaux. Vous pouvez y mettre des photos et voir en un coup d\'oeil quels biens sont occupés ou libres.'],
                    ['label' => 'Contrats de bail', 'desc' => 'Vous rédigez le bail dans l\'application et le locataire peut le signer depuis son compte. Pas besoin de se déplacer pour ça.'],
                    ['label' => 'Paiements et quittances', 'desc' => 'Notez les loyers reçus, peu importe comment le locataire paie : espèces, mobile money, virement ou chèque. La quittance se génère en un clic.'],
                    ['label' => 'Tickets de maintenance', 'desc' => 'Le locataire signale un problème depuis son compte. Vous le voyez, vous mettez à jour le statut et vous pouvez lui répondre directement.'],
                    ['label' => 'Deux comptes séparés', 'desc' => 'Le propriétaire et le locataire ont chacun leur propre interface. Ce que voit l\'un n\'est pas forcément visible par l\'autre.'],
                    ['label' => 'Historique des actions', 'desc' => 'Chaque action est enregistrée avec la date et l\'heure. Si vous avez besoin de vérifier quelque chose plus tard, c\'est là.'],
                ] as $feature)
                <div class="grid gap-2 py-5 sm:grid-cols-[200px_1fr] sm:gap-8">
                    <p class="font-semibold text-gray-900">{{ $feature['label'] }}</p>
                    <p class="text-sm leading-relaxed text-gray-500">{{ $feature['desc'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ── CTA final ────────────────────────────────────────────────────────── --}}
    <section class="border-t border-slate-200 bg-white py-16 px-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-3xl text-center">
            <h2 class="mb-3 text-2xl font-bold text-gray-900">Vous voulez l'essayer ?</h2>
            <p class="mb-8 text-gray-500">Créez votre compte et ajoutez votre premier bien. L'inscription prend deux minutes.</p>
            <a href="{{ route('register') }}" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-8 py-3.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">
                Créer mon compte
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </a>
            <p class="mt-4 text-xs text-gray-400">Déjà inscrit ? <a href="{{ route('login') }}" class="underline underline-offset-2 hover:text-gray-600">Se connecter</a></p>
        </div>
    </section>

    {{-- ── Pied de page ─────────────────────────────────────────────────────── --}}
    <footer class="border-t border-slate-200 bg-slate-50 py-6 px-4 sm:px-6 lg:px-8">
        <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-3 sm:flex-row">
            <div class="flex items-center gap-2 text-sm text-gray-400">
                <span class="flex h-6 w-6 items-center justify-center rounded-md bg-blue-600">
                    <svg class="h-3.5 w-3.5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </span>
                {{ config('app.name', 'ImmoPro') }}
            </div>
            <p class="text-xs text-gray-400">&copy; {{ date('Y') }} Jean Amassongon KODIO, Lomé Business School</p>
            <div class="flex gap-4 text-xs text-gray-400">
                <a href="{{ route('login') }}" class="hover:text-gray-600">Connexion</a>
                <a href="{{ route('register') }}" class="hover:text-gray-600">Inscription</a>
            </div>
        </div>
    </footer>

</body>
</html>
