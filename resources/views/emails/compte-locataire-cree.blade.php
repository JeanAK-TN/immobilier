<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Votre compte locataire</title>
    <style>
        body { margin: 0; padding: 0; background-color: #f8fafc; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; color: #1e293b; }
        .wrapper { max-width: 560px; margin: 40px auto; padding: 0 16px; }
        .card { background: #ffffff; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); overflow: hidden; }
        .header { background: #2563eb; padding: 32px; text-align: center; }
        .header-icon { display: inline-flex; align-items: center; justify-content: center; width: 52px; height: 52px; background: rgba(255,255,255,0.2); border-radius: 12px; margin-bottom: 16px; }
        .header h1 { margin: 0; color: #ffffff; font-size: 20px; font-weight: 700; }
        .header p { margin: 8px 0 0; color: #bfdbfe; font-size: 14px; }
        .body { padding: 32px; }
        .greeting { font-size: 16px; font-weight: 600; color: #1e293b; margin: 0 0 12px; }
        .text { font-size: 14px; line-height: 1.6; color: #475569; margin: 0 0 24px; }
        .credentials-box { background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 10px; padding: 20px; margin-bottom: 24px; }
        .credentials-title { font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #94a3b8; margin: 0 0 14px; }
        .credential-row { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; }
        .credential-row:last-child { margin-bottom: 0; }
        .credential-label { font-size: 12px; color: #94a3b8; width: 120px; flex-shrink: 0; }
        .credential-value { font-size: 14px; font-weight: 600; color: #1e293b; font-family: 'Courier New', Courier, monospace; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 6px; padding: 4px 10px; }
        .alert { background: #fefce8; border: 1px solid #fde68a; border-radius: 8px; padding: 14px 16px; margin-bottom: 24px; font-size: 13px; color: #78350f; line-height: 1.5; }
        .alert strong { color: #92400e; }
        .cta-btn { display: block; text-align: center; background: #2563eb; color: #ffffff; text-decoration: none; font-size: 15px; font-weight: 600; padding: 14px 24px; border-radius: 10px; margin-bottom: 24px; }
        .divider { border: none; border-top: 1px solid #e2e8f0; margin: 24px 0; }
        .footer-text { font-size: 12px; color: #94a3b8; line-height: 1.6; margin: 0; }
        .footer { padding: 20px 32px; text-align: center; border-top: 1px solid #f1f5f9; }
        .footer p { font-size: 11px; color: #cbd5e1; margin: 0; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">

            {{-- En-tête --}}
            <div class="header">
                <div class="header-icon">
                    <svg width="28" height="28" fill="none" stroke="#ffffff" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </div>
                <h1>{{ config('app.name') }}</h1>
                <p>Plateforme de gestion immobilière</p>
            </div>

            {{-- Corps --}}
            <div class="body">
                <p class="greeting">Bonjour {{ $locataire->prenom }} {{ $locataire->nom }},</p>

                <p class="text">
                    Votre propriétaire vous a créé un compte locataire sur <strong>{{ config('app.name') }}</strong>.
                    Grâce à cet espace, vous pouvez consulter votre contrat de bail, suivre vos paiements,
                    télécharger vos quittances et signaler des problèmes de maintenance.
                </p>

                {{-- Identifiants --}}
                <div class="credentials-box">
                    <p class="credentials-title">Vos identifiants de connexion</p>
                    <div class="credential-row">
                        <span class="credential-label">Adresse e-mail</span>
                        <span class="credential-value">{{ $locataire->email }}</span>
                    </div>
                    <div class="credential-row">
                        <span class="credential-label">Mot de passe</span>
                        <span class="credential-value">{{ $motDePasseTemporaire }}</span>
                    </div>
                </div>

                {{-- Avertissement --}}
                <div class="alert">
                    <strong>Important :</strong> Ce mot de passe est temporaire. Vous devrez le modifier lors de votre première connexion.
                    Conservez vos identifiants en lieu sûr et ne les partagez avec personne.
                </div>

                {{-- Bouton --}}
                <a href="{{ route('login') }}" class="cta-btn">
                    Accéder à mon espace locataire
                </a>

                <hr class="divider">

                <p class="footer-text">
                    Si vous n'êtes pas à l'origine de cette demande ou si vous pensez avoir reçu cet e-mail par erreur,
                    veuillez ignorer ce message ou contacter votre propriétaire directement.
                </p>
            </div>

            {{-- Pied de page --}}
            <div class="footer">
                <p>&copy; {{ date('Y') }} {{ config('app.name') }} — Jean Amassongon KODIO, Lomé Business School</p>
            </div>

        </div>
    </div>
</body>
</html>
