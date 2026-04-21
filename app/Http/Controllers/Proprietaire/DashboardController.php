<?php

namespace App\Http\Controllers\Proprietaire;

use App\Enums\StatutBien;
use App\Enums\StatutContrat;
use App\Http\Controllers\Controller;
use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Paiement;
use App\Models\TicketMaintenance;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $mois = now()->month;
        $annee = now()->year;

        $biensTotal = Bien::pourProprietaire($user)->count();
        $biensOccupes = Bien::pourProprietaire($user)->where('statut', StatutBien::Occupe)->count();

        $contratsActifs = Contrat::pourProprietaire($user)->actif()->count();
        $contratsEnAttente = Contrat::pourProprietaire($user)
            ->where('statut', StatutContrat::EnAttente)
            ->count();

        $paiementsMoisCount = Paiement::pourProprietaire($user)
            ->reussi()
            ->where('periode_mois', $mois)
            ->where('periode_annee', $annee)
            ->count();

        $paiementsMoisMontant = Paiement::pourProprietaire($user)
            ->reussi()
            ->where('periode_mois', $mois)
            ->where('periode_annee', $annee)
            ->sum('montant');

        $ticketsOuverts = TicketMaintenance::pourProprietaire($user)->actif()->count();

        $derniersContrats = Contrat::pourProprietaire($user)
            ->with(['bien', 'locataire'])
            ->latest()
            ->limit(5)
            ->get();

        return view('proprietaire.dashboard', compact(
            'biensTotal',
            'biensOccupes',
            'contratsActifs',
            'contratsEnAttente',
            'paiementsMoisCount',
            'paiementsMoisMontant',
            'ticketsOuverts',
            'derniersContrats',
        ));
    }
}
