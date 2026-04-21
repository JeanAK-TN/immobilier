<?php

namespace App\Http\Controllers\Locataire;

use App\Enums\ModePaiement;
use App\Enums\OperateurMobileMoney;
use App\Enums\StatutPaiement;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSimulatedPaiementRequest;
use App\Models\Paiement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaiementController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Paiement::class);

        $contrat = $request->user()?->locataire?->contratActif()?->load('bien');
        $paiements = $contrat
            ? $contrat->paiements()
                ->latest()
                ->paginate(10)
                ->withQueryString()
            : null;

        return view('locataire.paiements.index', [
            'contrat' => $contrat,
            'paiements' => $paiements,
            'modeOptions' => ModePaiement::cases(),
            'operateurOptions' => OperateurMobileMoney::cases(),
        ]);
    }

    public function store(StoreSimulatedPaiementRequest $request): RedirectResponse
    {
        $contrat = $request->user()?->locataire?->contratActif();

        abort_unless($contrat, 403);

        [$annee, $mois] = array_map('intval', explode('-', $request->validated('periode')));

        $doublon = $contrat->paiements()
            ->reussi()
            ->where('periode_annee', $annee)
            ->where('periode_mois', $mois)
            ->exists();

        $paiement = Paiement::create([
            'contrat_id' => $contrat->id,
            'periode_mois' => $mois,
            'periode_annee' => $annee,
            'montant' => $request->validated('montant'),
            'mode' => $request->validated('mode'),
            'operateur_mobile_money' => $request->validated('mode') === ModePaiement::MobileMoney->value
                ? $request->validated('operateur_mobile_money')
                : null,
            'reference' => Paiement::genererReference(),
            'statut' => StatutPaiement::SimuleReussi,
            'notes' => 'Paiement simulé - aucune transaction réelle.',
        ]);

        $redirect = redirect()
            ->route('locataire.paiements.show', $paiement)
            ->with('status', 'Le paiement simulé a été enregistré avec succès.');

        if ($doublon) {
            $redirect->with('warning', 'Un paiement simulé réussi existe déjà pour cette période. Cette nouvelle simulation a tout de même été enregistrée.');
        }

        return $redirect;
    }

    public function show(Paiement $paiement): View
    {
        $this->authorize('view', $paiement);

        $paiement->load(['contrat.bien', 'contrat.locataire', 'quittance']);

        return view('locataire.paiements.show', [
            'paiement' => $paiement,
        ]);
    }
}
