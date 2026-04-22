<?php

namespace App\Http\Controllers\Proprietaire;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLocataireRequest;
use App\Http\Requests\UpdateLocataireRequest;
use App\Mail\CompteLocataireCreeMail;
use App\Models\JournalAudit;
use App\Models\Locataire;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class LocataireController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Locataire::class);

        $recherche = trim((string) $request->query('recherche', ''));
        $statutCompte = trim((string) $request->query('statut_compte', ''));

        $locataires = Locataire::query()
            ->pourProprietaire($request->user())
            ->with('user')
            ->withCount([
                'contrats',
                'contrats as contrats_actifs_count' => fn ($query) => $query->where('statut', 'actif'),
            ])
            ->recherche($recherche)
            ->when($statutCompte === 'actif', fn ($query) => $query->whereHas('user', fn ($builder) => $builder->where('is_active', true)))
            ->when($statutCompte === 'inactif', fn ($query) => $query->whereHas('user', fn ($builder) => $builder->where('is_active', false)))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('proprietaire.locataires.index', [
            'locataires' => $locataires,
            'filtres' => compact('recherche', 'statutCompte'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $this->authorize('create', Locataire::class);

        return view('proprietaire.locataires.create', [
            'locataire' => new Locataire,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLocataireRequest $request): RedirectResponse
    {
        $motDePasseTemporaire = $this->genererMotDePasseTemporaire();
        $validated = $request->validated();

        $locataire = DB::transaction(function () use ($request, $validated, $motDePasseTemporaire): Locataire {
            $user = User::create([
                'name' => "{$validated['prenom']} {$validated['nom']}",
                'email' => $validated['email'],
                'password' => $motDePasseTemporaire,
                'role' => 'locataire',
                'must_change_password' => true,
                'is_active' => true,
            ]);

            $locataire = Locataire::create([
                'user_id' => $user->id,
                'cree_par_user_id' => $request->user()->id,
                'prenom' => $validated['prenom'],
                'nom' => $validated['nom'],
                'telephone' => $validated['telephone'],
                'email' => $validated['email'],
            ]);

            JournalAudit::enregistrer('creation_locataire', $locataire, [
                'email' => $locataire->email,
                'user_id' => $user->id,
            ]);

            return $locataire;
        });

        Mail::to($locataire->email)->send(new CompteLocataireCreeMail($locataire, $motDePasseTemporaire));

        return redirect()
            ->route('proprietaire.locataires.show', $locataire)
            ->with('status', 'Le locataire a bien été créé.')
            ->with('identifiants_locataire', [
                'email' => $validated['email'],
                'mot_de_passe_temporaire' => $motDePasseTemporaire,
            ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Locataire $locataire): View
    {
        $this->authorize('view', $locataire);

        $locataire->load('user')
            ->loadCount([
                'contrats',
                'contrats as contrats_actifs_count' => fn ($query) => $query->where('statut', 'actif'),
            ]);

        return view('proprietaire.locataires.show', [
            'locataire' => $locataire,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Locataire $locataire): View
    {
        $this->authorize('update', $locataire);

        $locataire->load('user');

        return view('proprietaire.locataires.edit', [
            'locataire' => $locataire,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLocataireRequest $request, Locataire $locataire): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $locataire): void {
            $locataire->user()->update([
                'name' => "{$validated['prenom']} {$validated['nom']}",
                'email' => $validated['email'],
                'is_active' => $validated['is_active'],
            ]);

            $locataire->update([
                'prenom' => $validated['prenom'],
                'nom' => $validated['nom'],
                'telephone' => $validated['telephone'],
                'email' => $validated['email'],
            ]);
        });

        JournalAudit::enregistrer('mise_a_jour_locataire', $locataire, [
            'compte_actif' => $validated['is_active'],
        ]);

        return redirect()
            ->route('proprietaire.locataires.show', $locataire)
            ->with('status', 'Le locataire a bien été mis à jour.');
    }

    /**
     * Toggle the specified locataire account activation.
     */
    public function toggleActivation(Locataire $locataire): RedirectResponse
    {
        $this->authorize('update', $locataire);

        $user = $locataire->user;
        $user->forceFill([
            'is_active' => ! $user->is_active,
        ])->save();

        JournalAudit::enregistrer('statut_compte_locataire', $locataire, [
            'compte_actif' => $user->is_active,
        ]);

        $message = $user->is_active
            ? 'Le compte locataire a bien été réactivé.'
            : 'Le compte locataire a bien été désactivé.';

        return back()->with('status', $message);
    }

    private function genererMotDePasseTemporaire(): string
    {
        return 'Loc'.Str::upper(Str::random(4)).'!'.random_int(100, 999);
    }
}
