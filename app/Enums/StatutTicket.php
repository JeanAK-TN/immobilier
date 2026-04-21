<?php

namespace App\Enums;

enum StatutTicket: string
{
    case Ouvert = 'ouvert';
    case EnCours = 'en_cours';
    case EnAttenteLocataire = 'en_attente_locataire';
    case Resolu = 'resolu';
    case Ferme = 'ferme';

    public function label(): string
    {
        return match ($this) {
            StatutTicket::Ouvert => 'Ouvert',
            StatutTicket::EnCours => 'En cours',
            StatutTicket::EnAttenteLocataire => 'En attente du locataire',
            StatutTicket::Resolu => 'Résolu',
            StatutTicket::Ferme => 'Fermé',
        };
    }

    public function isActif(): bool
    {
        return in_array($this, [StatutTicket::Ouvert, StatutTicket::EnCours, StatutTicket::EnAttenteLocataire]);
    }
}
