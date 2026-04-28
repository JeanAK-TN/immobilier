<?php

namespace App\Http\Controllers\Locataire;

use App\Http\Controllers\Controller;
use App\Models\Contrat;
use App\Models\Paiement;
use App\Models\Quittance;
use App\Models\TicketMaintenance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $contrat = $user?->locataire?->contratCourant()?->load('bien');

        $prochainPaiement = null;
        $derniereQuittance = null;

        if ($contrat?->isActif()) {
            $prochainPaiement = $this->calculerProchainPaiement($contrat);

            $derniereQuittance = Quittance::query()
                ->whereHas('contrat.locataire', fn ($q) => $q->where('user_id', $user->id))
                ->with(['paiement', 'contrat.bien'])
                ->latest('emise_le')
                ->first();
        }

        $ticketsActifsCount = $user
            ? TicketMaintenance::query()->pourLocataire($user)->actif()->count()
            : 0;

        return view('locataire.dashboard', [
            'contrat' => $contrat,
            'prochainPaiement' => $prochainPaiement,
            'derniereQuittance' => $derniereQuittance,
            'ticketsActifsCount' => $ticketsActifsCount,
        ]);
    }

    /**
     * @return array{date: Carbon, montant: float, statut: 'a_jour'|'a_payer'|'en_retard'}
     */
    private function calculerProchainPaiement(Contrat $contrat): array
    {
        $paiementCourantExiste = Paiement::query()
            ->where('contrat_id', $contrat->id)
            ->where('periode_mois', now()->month)
            ->where('periode_annee', now()->year)
            ->reussi()
            ->exists();

        if ($paiementCourantExiste) {
            $dateNext = now()->addMonth();
            $prochaineDate = Carbon::create($dateNext->year, $dateNext->month, $contrat->jour_paiement);
            $statut = 'a_jour';
        } else {
            $prochaineDate = Carbon::create(now()->year, now()->month, $contrat->jour_paiement);
            $statut = $prochaineDate->isPast() ? 'en_retard' : 'a_payer';
        }

        return [
            'date' => $prochaineDate,
            'montant' => $contrat->montantTotalMensuel(),
            'statut' => $statut,
        ];
    }
}
