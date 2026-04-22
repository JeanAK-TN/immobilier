<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nouveau ticket de maintenance</title>
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
        .info-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 20px; margin-bottom: 24px; }
        .info-row { margin-bottom: 14px; }
        .info-row:last-child { margin-bottom: 0; }
        .info-label { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #94a3b8; margin: 0 0 4px; }
        .info-value { font-size: 14px; color: #1e293b; margin: 0; }
        .info-value strong { font-weight: 600; }
        .description-box { background: #f8fafc; border-left: 3px solid #2563eb; border-radius: 0 8px 8px 0; padding: 14px 16px; margin-bottom: 24px; font-size: 14px; line-height: 1.6; color: #475569; white-space: pre-line; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 999px; font-size: 12px; font-weight: 600; }
        .badge-priority-haute { background: #fee2e2; color: #991b1b; }
        .badge-priority-moyenne { background: #fef3c7; color: #92400e; }
        .badge-priority-basse { background: #f1f5f9; color: #475569; }
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
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <h1>Nouveau ticket de maintenance</h1>
                <p>{{ config('app.name') }}</p>
            </div>

            {{-- Corps --}}
            <div class="body">
                <p class="greeting">Bonjour {{ $ticket->contrat->bien->proprietaire->name }},</p>

                <p class="text">
                    {{ $ticket->contrat->locataire->nomComplet() }} a ouvert un ticket de maintenance pour le bien
                    <strong>{{ $ticket->contrat->bien->nom }}</strong>.
                </p>

                {{-- Infos du ticket --}}
                <div class="info-box">
                    <div class="info-row">
                        <p class="info-label">Titre</p>
                        <p class="info-value"><strong>{{ $ticket->titre }}</strong></p>
                    </div>
                    <div class="info-row">
                        <p class="info-label">Catégorie</p>
                        <p class="info-value">{{ $ticket->categorie->label() }}</p>
                    </div>
                    <div class="info-row">
                        <p class="info-label">Priorité</p>
                        <p class="info-value">
                            <span class="badge badge-priority-{{ $ticket->priorite->value }}">
                                {{ $ticket->priorite->label() }}
                            </span>
                        </p>
                    </div>
                    <div class="info-row">
                        <p class="info-label">Bien concerné</p>
                        <p class="info-value">{{ $ticket->contrat->bien->nom }} — {{ $ticket->contrat->bien->adresse }}, {{ $ticket->contrat->bien->ville }}</p>
                    </div>
                    <div class="info-row">
                        <p class="info-label">Locataire</p>
                        <p class="info-value">{{ $ticket->contrat->locataire->nomComplet() }} &lt;{{ $ticket->contrat->locataire->email }}&gt;</p>
                    </div>
                    <div class="info-row">
                        <p class="info-label">Soumis le</p>
                        <p class="info-value">{{ $ticket->created_at->translatedFormat('d F Y à H:i') }}</p>
                    </div>
                </div>

                {{-- Description --}}
                <p style="font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #94a3b8; margin: 0 0 8px;">Description</p>
                <div class="description-box">{{ $ticket->description }}</div>

                {{-- CTA --}}
                <a href="{{ route('proprietaire.tickets.show', $ticket) }}" class="cta-btn">
                    Voir le ticket et répondre
                </a>

                <hr class="divider">

                <p class="footer-text">
                    Vous recevez cet e-mail car vous êtes le propriétaire du bien concerné.
                    Connectez-vous pour traiter ce ticket et communiquer avec votre locataire.
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
