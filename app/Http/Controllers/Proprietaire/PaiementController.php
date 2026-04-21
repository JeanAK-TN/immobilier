<?php

namespace App\Http\Controllers\Proprietaire;

use App\Enums\StatutPaiement;
use App\Http\Controllers\Controller;
use App\Models\Bien;
use App\Models\Locataire;
use App\Models\Paiement;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaiementController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Paiement::class);

        $bienId = trim((string) $request->query('bien_id', ''));
        $locataireId = trim((string) $request->query('locataire_id', ''));
        $statut = trim((string) $request->query('statut', ''));
        $periode = trim((string) $request->query('periode', ''));

        [$annee, $mois] = $periode !== '' && str_contains($periode, '-')
            ? array_map('intval', explode('-', $periode))
            : [null, null];

        $paiements = Paiement::query()
            ->pourProprietaire($request->user())
            ->with(['contrat.bien', 'contrat.locataire'])
            ->when($bienId !== '', fn ($query) => $query->whereHas('contrat', fn ($contratQuery) => $contratQuery->where('bien_id', $bienId)))
            ->when($locataireId !== '', fn ($query) => $query->whereHas('contrat', fn ($contratQuery) => $contratQuery->where('locataire_id', $locataireId)))
            ->when($statut !== '', fn ($query) => $query->where('statut', $statut))
            ->when($annee && $mois, fn ($query) => $query->where('periode_annee', $annee)->where('periode_mois', $mois))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('proprietaire.paiements.index', [
            'paiements' => $paiements,
            'biens' => Bien::query()->pourProprietaire($request->user())->orderBy('nom')->get(),
            'locataires' => Locataire::query()->pourProprietaire($request->user())->orderBy('prenom')->orderBy('nom')->get(),
            'statutOptions' => StatutPaiement::cases(),
            'filtres' => compact('bienId', 'locataireId', 'statut', 'periode'),
        ]);
    }

    public function show(Paiement $paiement): View
    {
        $this->authorize('view', $paiement);

        $paiement->load(['contrat.bien', 'contrat.locataire']);

        return view('proprietaire.paiements.show', [
            'paiement' => $paiement,
        ]);
    }
}
