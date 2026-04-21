<?php

namespace App\Http\Controllers\Locataire;

use App\Enums\StatutContrat;
use App\Http\Controllers\Controller;
use App\Http\Requests\SignerContratRequest;
use App\Models\Contrat;
use App\Models\JournalAudit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MonContratController extends Controller
{
    public function show(Request $request): View
    {
        $contrat = $this->contratCourant($request);

        if ($contrat) {
            $this->authorize('view', $contrat);
        }

        return view('locataire.contrat', [
            'contrat' => $contrat,
        ]);
    }

    public function downloadDocument(Request $request): StreamedResponse
    {
        $contrat = $this->contratCourant($request);

        abort_unless($contrat, 404);
        $this->authorize('view', $contrat);
        abort_unless($contrat->documentDisponible(), 404);

        return Storage::disk('local')->download(
            $contrat->document_path,
            $contrat->nomDocument() ?? 'contrat.pdf'
        );
    }

    public function sign(SignerContratRequest $request): RedirectResponse
    {
        $contrat = $this->contratCourant($request);

        abort_unless($contrat, 404);
        $this->authorize('sign', $contrat);

        DB::transaction(function () use ($contrat, $request): void {
            $ancienStatut = $contrat->statut->value;

            $contrat->update([
                'statut' => StatutContrat::Actif,
                'signe_le' => now(),
                'signe_nom' => $request->validated('signe_nom'),
                'signe_ip' => $request->ip(),
            ]);

            $contrat->bien->synchroniserStatutOccupation();

            JournalAudit::enregistrer('signature_contrat_locataire', $contrat, [
                'ancien_statut' => $ancienStatut,
                'nouveau_statut' => $contrat->statut->value,
                'signe_nom' => $contrat->signe_nom,
                'signe_ip' => $contrat->signe_ip,
            ]);
        });

        return redirect()
            ->route('locataire.contrat.show')
            ->with('status', 'Votre contrat a bien été signé.');
    }

    private function contratCourant(Request $request): ?Contrat
    {
        return $request->user()?->locataire?->contratCourant()?->load([
            'bien.proprietaire',
            'locataire',
        ]);
    }
}
