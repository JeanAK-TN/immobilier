<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'ImmoPro') }} — Gestion immobilière simplifiée</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-white text-gray-900">

    {{-- Navigation --}}
    <nav class="fixed inset-x-0 top-0 z-50 bg-white/90 backdrop-blur border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-3">
                    <div class="flex items-center justify-center w-9 h-9 rounded-xl bg-blue-600">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                    </div>
                    <span class="text-lg font-semibold text-gray-900">{{ config('app.name', 'ImmoPro') }}</span>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors px-4 py-2 rounded-lg hover:bg-gray-100">
                        Se connecter
                    </a>
                    <a href="{{ route('register') }}" class="text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 transition-colors px-4 py-2 rounded-lg">
                        Créer un compte
                    </a>
                </div>
            </div>
        </div>
    </nav>

    {{-- Héros --}}
    <section class="pt-32 pb-20 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto text-center">
            <span class="inline-flex items-center gap-2 bg-blue-50 text-blue-700 text-xs font-semibold px-3 py-1.5 rounded-full mb-6">
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
                Plateforme de gestion immobilière
            </span>

            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-gray-900 leading-tight mb-6">
                Gérez vos biens immobiliers
                <span class="text-blue-600"> en toute simplicité</span>
            </h1>

            <p class="text-lg sm:text-xl text-gray-500 max-w-2xl mx-auto mb-10 leading-relaxed">
                Une solution complète pour les propriétaires et locataires : contrats, paiements, quittances et suivi des interventions — tout en un seul endroit.
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('register') }}" class="inline-flex items-center justify-center gap-2 px-8 py-3.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition-colors shadow-lg shadow-blue-200">
                    Commencer gratuitement
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
                <a href="{{ route('login') }}" class="inline-flex items-center justify-center gap-2 px-8 py-3.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition-colors">
                    J'ai déjà un compte
                </a>
            </div>
        </div>
    </section>

    {{-- Fonctionnalités --}}
    <section class="py-20 bg-slate-50 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Tout ce dont vous avez besoin</h2>
                <p class="text-gray-500 max-w-xl mx-auto">Des outils pensés pour simplifier la gestion de vos biens, de vos locataires et de vos paiements.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                {{-- Gestion des biens --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <div class="w-11 h-11 rounded-xl bg-blue-50 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-semibold text-gray-900 mb-2">Gestion des biens</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">Ajoutez et gérez vos appartements, maisons, bureaux et locaux commerciaux avec photos et descriptions détaillées.</p>
                </div>

                {{-- Contrats de location --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <div class="w-11 h-11 rounded-xl bg-green-50 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-semibold text-gray-900 mb-2">Contrats de location</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">Créez et gérez vos baux locatifs. Les locataires peuvent signer électroniquement depuis leur espace personnel.</p>
                </div>

                {{-- Suivi des paiements --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <div class="w-11 h-11 rounded-xl bg-yellow-50 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-semibold text-gray-900 mb-2">Suivi des paiements</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">Enregistrez les loyers perçus par mobile money, virement, espèces ou chèque et consultez l'historique complet.</p>
                </div>

                {{-- Quittances PDF --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <div class="w-11 h-11 rounded-xl bg-purple-50 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-semibold text-gray-900 mb-2">Quittances PDF</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">Générez et téléchargez des quittances de loyer au format PDF en quelques secondes, accessibles par le locataire.</p>
                </div>

                {{-- Tickets de maintenance --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <div class="w-11 h-11 rounded-xl bg-red-50 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-semibold text-gray-900 mb-2">Tickets de maintenance</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">Les locataires signalent les problèmes directement depuis leur espace. Suivez et gérez chaque demande d'intervention.</p>
                </div>

                {{-- Espace locataire --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <div class="w-11 h-11 rounded-xl bg-teal-50 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-semibold text-gray-900 mb-2">Espace locataire</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">Chaque locataire dispose d'un accès personnel pour consulter son contrat, ses paiements et ses quittances à tout moment.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Deux profils --}}
    <section class="py-20 px-4 sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto">
            <div class="text-center mb-14">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Une plateforme, deux espaces</h2>
                <p class="text-gray-500">Propriétaires et locataires disposent chacun d'un tableau de bord adapté à leurs besoins.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- Propriétaire --}}
                <div class="rounded-2xl bg-blue-600 p-8 text-white">
                    <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center mb-5">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Espace propriétaire</h3>
                    <ul class="space-y-2.5 text-blue-100 text-sm">
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 mt-0.5 text-blue-200 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            Gérer plusieurs biens immobiliers
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 mt-0.5 text-blue-200 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            Créer et activer les comptes locataires
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 mt-0.5 text-blue-200 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            Rédiger et suivre les contrats de bail
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 mt-0.5 text-blue-200 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            Émettre des quittances de loyer PDF
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 mt-0.5 text-blue-200 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            Traiter les demandes de maintenance
                        </li>
                    </ul>
                    <a href="{{ route('register') }}" class="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-white hover:underline">
                        Créer mon espace propriétaire
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </a>
                </div>

                {{-- Locataire --}}
                <div class="rounded-2xl bg-gray-900 p-8 text-white">
                    <div class="w-12 h-12 rounded-xl bg-white/10 flex items-center justify-center mb-5">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Espace locataire</h3>
                    <ul class="space-y-2.5 text-gray-300 text-sm">
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 mt-0.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            Consulter et signer son contrat de bail
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 mt-0.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            Effectuer et suivre ses paiements de loyer
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 mt-0.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            Télécharger ses quittances au format PDF
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 mt-0.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            Ouvrir un ticket de signalement de panne
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 mt-0.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            Accès sécurisé par identifiants fournis par le propriétaire
                        </li>
                    </ul>
                    <a href="{{ route('login') }}" class="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-white hover:underline">
                        Accéder à mon espace
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA final --}}
    <section class="py-20 px-4 sm:px-6 lg:px-8 bg-slate-50">
        <div class="max-w-3xl mx-auto text-center">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Prêt à simplifier votre gestion immobilière ?</h2>
            <p class="text-gray-500 mb-8">Rejoignez les propriétaires qui ont choisi une solution moderne, claire et efficace.</p>
            <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-8 py-3.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition-colors shadow-lg shadow-blue-200">
                Créer mon compte gratuitement
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </a>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="border-t border-gray-100 bg-white py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2 text-sm text-gray-400">
                <div class="flex items-center justify-center w-6 h-6 rounded-md bg-blue-600">
                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </div>
                <span>{{ config('app.name', 'ImmoPro') }}</span>
            </div>
            <p class="text-xs text-gray-400">
                &copy; {{ date('Y') }} Jean Amassongon KODIO, Lomé Business School
            </p>
            <div class="flex items-center gap-4 text-sm">
                <a href="{{ route('login') }}" class="text-gray-400 hover:text-gray-600 transition-colors">Connexion</a>
                <a href="{{ route('register') }}" class="text-gray-400 hover:text-gray-600 transition-colors">Inscription</a>
            </div>
        </div>
    </footer>

</body>
</html>
