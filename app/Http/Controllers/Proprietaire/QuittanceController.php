<?php

namespace App\Http\Controllers\Proprietaire;

use App\Http\Controllers\Controller;
use App\Models\Contrat;
use App\Models\JournalAudit;
use App\Models\Paiement;
use App\Models\Quittance;
use App\QuittancePdfBuilder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class QuittanceController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Quittance::class);

        $contratId = trim((string) $request->query('contrat_id', ''));
        $periode = trim((string) $request->query('periode', ''));
        [$annee, $mois] = $periode !== '' && str_contains($periode, '-')
            ? array_map('intval', explode('-', $periode))
            : [null, null];

        $quittances = Quittance::query()
            ->pourProprietaire($request->user())
            ->with(['contrat.bien', 'contrat.locataire', 'paiement'])
            ->when($contratId !== '', fn ($query) => $query->where('contrat_id', $contratId))
            ->when($annee && $mois, fn ($query) => $query->where('periode_annee', $annee)->where('periode_mois', $mois))
            ->latest('emise_le')
            ->paginate(10)
            ->withQueryString();

        return view('proprietaire.quittances.index', [
            'quittances' => $quittances,
            'contrats' => Contrat::query()
                ->pourProprietaire($request->user())
                ->with(['bien', 'locataire'])
                ->orderByDesc('date_debut')
                ->get(),
            'filtres' => compact('contratId', 'periode'),
        ]);
    }

    public function store(Paiement $paiement, Request $request, QuittancePdfBuilder $pdfBuilder): RedirectResponse
    {
        $paiement->load(['contrat.bien.proprietaire', 'contrat.locataire', 'quittance']);

        $this->authorize('create', [Quittance::class, $paiement]);

        $existante = Quittance::query()
            ->where('contrat_id', $paiement->contrat_id)
            ->where('periode_annee', $paiement->periode_annee)
            ->where('periode_mois', $paiement->periode_mois)
            ->first();

        if ($existante) {
            return redirect()
                ->route('proprietaire.paiements.show', $paiement)
                ->with('warning', 'Une quittance existe déjà pour cette période et ce contrat.');
        }

        $quittance = DB::transaction(function () use ($paiement, $request, $pdfBuilder): Quittance {
            $numero = Quittance::genererNumero();
            $path = sprintf('quittances/%s.pdf', strtolower($numero));

            $quittance = Quittance::create([
                'contrat_id' => $paiement->contrat_id,
                'paiement_id' => $paiement->id,
                'generee_par_user_id' => $request->user()->id,
                'periode_mois' => $paiement->periode_mois,
                'periode_annee' => $paiement->periode_annee,
                'numero_quittance' => $numero,
                'emise_le' => now(),
                'fichier_path' => $path,
            ]);

            Storage::disk('local')->put($path, $pdfBuilder->build($quittance));

            JournalAudit::enregistrer('generation_quittance', $quittance, [
                'numero_quittance' => $quittance->numero_quittance,
                'contrat_id' => $quittance->contrat_id,
                'paiement_id' => $quittance->paiement_id,
            ]);

            return $quittance;
        });

        return redirect()
            ->route('proprietaire.paiements.show', $paiement)
            ->with('status', sprintf('La quittance %s a bien été générée.', $quittance->numero_quittance));
    }

    public function download(Quittance $quittance): StreamedResponse
    {
        $this->authorize('view', $quittance);

        abort_unless($quittance->documentDisponible(), 404);

        return Storage::disk('local')->download(
            $quittance->fichier_path,
            $quittance->nomFichier()
        );
    }
}
