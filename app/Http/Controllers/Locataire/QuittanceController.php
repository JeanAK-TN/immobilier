<?php

namespace App\Http\Controllers\Locataire;

use App\Http\Controllers\Controller;
use App\Models\Quittance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class QuittanceController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Quittance::class);

        $quittances = Quittance::query()
            ->pourLocataire($request->user())
            ->with(['contrat.bien', 'paiement'])
            ->latest('emise_le')
            ->paginate(10)
            ->withQueryString();

        return view('locataire.quittances.index', [
            'quittances' => $quittances,
        ]);
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
