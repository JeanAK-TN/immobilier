<?php

namespace App\Http\Controllers\Proprietaire;

use App\Enums\StatutBien;
use App\Enums\TypeBien;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBienRequest;
use App\Http\Requests\UpdateBienRequest;
use App\Models\Bien;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BienController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Bien::class);

        $recherche = trim((string) $request->query('recherche', ''));
        $type = trim((string) $request->query('type', ''));
        $statut = trim((string) $request->query('statut', ''));
        $occupation = trim((string) $request->query('occupation', ''));

        $biens = Bien::query()
            ->pourProprietaire($request->user())
            ->withCount(['photos', 'contratActif'])
            ->recherche($recherche)
            ->when($type !== '', fn ($query) => $query->where('type', $type))
            ->when($statut !== '', fn ($query) => $query->where('statut', $statut))
            ->when($occupation === 'occupe', fn ($query) => $query->has('contratActif'))
            ->when($occupation === 'disponible', fn ($query) => $query->doesntHave('contratActif'))
            ->latest()
            ->paginate(9)
            ->withQueryString();

        return view('proprietaire.biens.index', [
            'biens' => $biens,
            'filtres' => compact('recherche', 'type', 'statut', 'occupation'),
            ...$this->optionsFormulaire(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $this->authorize('create', Bien::class);

        return view('proprietaire.biens.create', [
            'bien' => new Bien([
                'pays' => 'France',
                'statut' => StatutBien::Disponible,
            ]),
            ...$this->optionsFormulaire(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBienRequest $request): RedirectResponse
    {
        $bien = Bien::create([
            ...Arr::except($request->validated(), ['photos']),
            'user_id' => $request->user()->id,
        ]);

        $this->enregistrerPhotos($bien, $request->file('photos', []), $request->user());

        return redirect()
            ->route('proprietaire.biens.show', $bien)
            ->with('status', 'Le bien a bien été créé.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Bien $bien): View
    {
        $this->authorize('view', $bien);

        $bien->load([
            'photos' => fn ($query) => $query->latest(),
            'contratActif' => fn ($query) => $query->with('locataire')->latest('date_debut'),
        ])->loadCount(['photos', 'contratActif']);

        return view('proprietaire.biens.show', [
            'bien' => $bien,
            'contratActif' => $bien->contratActif->first(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bien $bien): View
    {
        $this->authorize('update', $bien);

        $bien->load([
            'photos' => fn ($query) => $query->latest(),
        ])->loadCount(['contratActif']);

        return view('proprietaire.biens.edit', [
            'bien' => $bien,
            ...$this->optionsFormulaire(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBienRequest $request, Bien $bien): RedirectResponse
    {
        $validated = $request->validated();

        $bien->update(Arr::except($validated, ['photos', 'photos_a_supprimer']));

        $this->supprimerPhotos($bien, $validated['photos_a_supprimer'] ?? []);
        $this->enregistrerPhotos($bien, $request->file('photos', []), $request->user());

        return redirect()
            ->route('proprietaire.biens.show', $bien)
            ->with('status', 'Le bien a bien été mis à jour.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bien $bien): RedirectResponse
    {
        $this->authorize('delete', $bien);

        $this->supprimerPhotos($bien, $bien->photos()->pluck('id')->all());
        $bien->delete();

        return redirect()
            ->route('proprietaire.biens.index')
            ->with('status', 'Le bien a bien été supprimé.');
    }

    /**
     * @return array{typeOptions: array<int, TypeBien>, statutOptions: array<int, StatutBien>}
     */
    private function optionsFormulaire(): array
    {
        return [
            'typeOptions' => TypeBien::cases(),
            'statutOptions' => StatutBien::cases(),
        ];
    }

    /**
     * @param  array<int, UploadedFile>  $photos
     */
    private function enregistrerPhotos(Bien $bien, array $photos, User $user): void
    {
        foreach ($photos as $photo) {
            $chemin = $photo->store("biens/{$bien->id}/photos", 'public');

            $bien->photos()->create([
                'uploade_par_user_id' => $user->id,
                'nom_fichier' => basename($chemin),
                'nom_original' => $photo->getClientOriginalName(),
                'chemin' => $chemin,
                'type_mime' => $photo->getMimeType() ?? 'application/octet-stream',
                'taille' => $photo->getSize(),
            ]);
        }
    }

    /**
     * @param  array<int, int|string>  $photoIds
     */
    private function supprimerPhotos(Bien $bien, array $photoIds): void
    {
        $photos = $bien->photos()->whereKey($photoIds)->get();

        if ($photos->isEmpty()) {
            return;
        }

        Storage::disk('public')->delete($photos->pluck('chemin')->all());
        $bien->photos()->whereKey($photos->pluck('id'))->delete();
    }
}
