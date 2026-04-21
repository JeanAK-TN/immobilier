<?php

namespace App\Http\Controllers\Proprietaire;

use App\Enums\StatutContrat;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContratRequest;
use App\Http\Requests\UpdateContratRequest;
use App\Models\Bien;
use App\Models\Contrat;
use App\Models\JournalAudit;
use App\Models\Locataire;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ContratController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Contrat::class);

        $recherche = trim((string) $request->query('recherche', ''));
        $statut = trim((string) $request->query('statut', ''));
        $bienId = trim((string) $request->query('bien_id', ''));
        $locataireId = trim((string) $request->query('locataire_id', ''));

        $contrats = Contrat::query()
            ->pourProprietaire($request->user())
            ->with(['bien', 'locataire'])
            ->recherche($recherche)
            ->when($statut !== '', fn ($query) => $query->where('statut', $statut))
            ->when($bienId !== '', fn ($query) => $query->where('bien_id', $bienId))
            ->when($locataireId !== '', fn ($query) => $query->where('locataire_id', $locataireId))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('proprietaire.contrats.index', [
            'contrats' => $contrats,
            'filtres' => compact('recherche', 'statut', 'bienId', 'locataireId'),
            ...$this->formOptions($request->user()),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $this->authorize('create', Contrat::class);

        return view('proprietaire.contrats.create', [
            'contrat' => new Contrat([
                'jour_paiement' => 5,
                'statut' => StatutContrat::Brouillon,
                'reconduction_auto' => false,
                'depot_garantie' => 0,
                'charges' => 0,
            ]),
            ...$this->formOptions($request->user()),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreContratRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $contrat = DB::transaction(function () use ($validated): Contrat {
            $contrat = Contrat::create([
                ...Arr::except($validated, ['document_pdf']),
                'document_path' => $this->stockerDocument($validated['document_pdf'] ?? null),
            ]);

            $this->synchroniserOccupationBien((int) $contrat->bien_id);

            JournalAudit::enregistrer('creation_contrat', $contrat, [
                'statut' => $contrat->statut->value,
                'bien_id' => $contrat->bien_id,
                'locataire_id' => $contrat->locataire_id,
            ]);

            return $contrat;
        });

        return redirect()
            ->route('proprietaire.contrats.show', $contrat)
            ->with('status', 'Le contrat a bien été créé.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Contrat $contrat): View
    {
        $this->authorize('view', $contrat);

        $contrat->load(['bien', 'locataire', 'paiements', 'quittances']);

        $audits = JournalAudit::query()
            ->where('modele_type', Contrat::class)
            ->where('modele_id', $contrat->id)
            ->latest('created_at')
            ->get();

        return view('proprietaire.contrats.show', [
            'contrat' => $contrat,
            'audits' => $audits,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Contrat $contrat): View
    {
        $this->authorize('update', $contrat);

        $contrat->load(['bien', 'locataire']);

        return view('proprietaire.contrats.edit', [
            'contrat' => $contrat,
            ...$this->formOptions($request->user()),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateContratRequest $request, Contrat $contrat): RedirectResponse
    {
        $validated = $request->validated();
        $ancienBienId = (int) $contrat->bien_id;
        $ancienStatut = $contrat->statut->value;
        $ancienDocument = $contrat->document_path;

        DB::transaction(function () use ($validated, $contrat, $ancienBienId, $ancienStatut, $ancienDocument): void {
            $nouveauDocument = $this->remplacerDocument(
                $ancienDocument,
                $validated['document_pdf'] ?? null
            );

            $contrat->update([
                ...Arr::except($validated, ['document_pdf']),
                'document_path' => $nouveauDocument,
            ]);

            $this->synchroniserOccupationBien($ancienBienId);
            $this->synchroniserOccupationBien((int) $contrat->bien_id);

            JournalAudit::enregistrer('mise_a_jour_contrat', $contrat, [
                'ancien_statut' => $ancienStatut,
                'nouveau_statut' => $contrat->statut->value,
                'bien_id' => $contrat->bien_id,
                'locataire_id' => $contrat->locataire_id,
                'document_mis_a_jour' => filled($validated['document_pdf'] ?? null),
            ]);
        });

        return redirect()
            ->route('proprietaire.contrats.show', $contrat)
            ->with('status', 'Le contrat a bien été mis à jour.');
    }

    /**
     * Download the PDF document attached to the contract.
     */
    public function downloadDocument(Contrat $contrat): StreamedResponse
    {
        $this->authorize('view', $contrat);

        abort_unless($contrat->documentDisponible(), 404);

        return Storage::disk('local')->download(
            $contrat->document_path,
            $contrat->nomDocument() ?? 'contrat.pdf'
        );
    }

    /**
     * @return array{
     *     biens: Collection<int, Bien>,
     *     locataires: Collection<int, Locataire>,
     *     statutOptions: array<int, StatutContrat>
     * }
     */
    private function formOptions(User $user): array
    {
        return [
            'biens' => Bien::query()
                ->pourProprietaire($user)
                ->orderBy('nom')
                ->get(),
            'locataires' => Locataire::query()
                ->pourProprietaire($user)
                ->orderBy('prenom')
                ->orderBy('nom')
                ->get(),
            'statutOptions' => StatutContrat::cases(),
        ];
    }

    private function synchroniserOccupationBien(int $bienId): void
    {
        $bien = Bien::query()->find($bienId);

        if (! $bien) {
            return;
        }

        $bien->synchroniserStatutOccupation();
    }

    private function stockerDocument(mixed $document): ?string
    {
        if (! $document) {
            return null;
        }

        return $document->store('contrats/documents', 'local');
    }

    private function remplacerDocument(?string $ancienDocument, mixed $nouveauDocument): ?string
    {
        if (! $nouveauDocument) {
            return $ancienDocument;
        }

        if ($ancienDocument) {
            Storage::disk('local')->delete($ancienDocument);
        }

        return $this->stockerDocument($nouveauDocument);
    }
}
