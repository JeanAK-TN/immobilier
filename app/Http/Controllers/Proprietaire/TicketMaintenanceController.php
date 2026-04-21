<?php

namespace App\Http\Controllers\Proprietaire;

use App\Enums\CategorieTicket;
use App\Enums\PrioriteTicket;
use App\Enums\StatutTicket;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateTicketMaintenanceStatusRequest;
use App\Models\Bien;
use App\Models\JournalAudit;
use App\Models\Locataire;
use App\Models\TicketMaintenance;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TicketMaintenanceController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', TicketMaintenance::class);

        $recherche = trim((string) $request->query('recherche', ''));
        $statut = trim((string) $request->query('statut', ''));
        $priorite = trim((string) $request->query('priorite', ''));
        $categorie = trim((string) $request->query('categorie', ''));
        $bienId = trim((string) $request->query('bien_id', ''));
        $locataireId = trim((string) $request->query('locataire_id', ''));

        $tickets = TicketMaintenance::query()
            ->pourProprietaire($request->user())
            ->with(['contrat.bien', 'contrat.locataire'])
            ->withCount('messages')
            ->recherche($recherche)
            ->when($statut !== '', fn ($query) => $query->where('statut', $statut))
            ->when($priorite !== '', fn ($query) => $query->where('priorite', $priorite))
            ->when($categorie !== '', fn ($query) => $query->where('categorie', $categorie))
            ->when($bienId !== '', fn ($query) => $query->whereHas('contrat', fn ($contratQuery) => $contratQuery->where('bien_id', $bienId)))
            ->when($locataireId !== '', fn ($query) => $query->whereHas('contrat', fn ($contratQuery) => $contratQuery->where('locataire_id', $locataireId)))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('proprietaire.tickets.index', [
            'tickets' => $tickets,
            'biens' => Bien::query()->pourProprietaire($request->user())->orderBy('nom')->get(),
            'locataires' => Locataire::query()->pourProprietaire($request->user())->orderBy('prenom')->orderBy('nom')->get(),
            'categorieOptions' => CategorieTicket::cases(),
            'prioriteOptions' => PrioriteTicket::cases(),
            'statutOptions' => StatutTicket::cases(),
            'filtres' => compact('recherche', 'statut', 'priorite', 'categorie', 'bienId', 'locataireId'),
        ]);
    }

    public function show(TicketMaintenance $ticket): View
    {
        $this->authorize('view', $ticket);

        $ticket->load([
            'contrat.bien',
            'contrat.locataire.user',
            'piecesJointes' => fn ($query) => $query->latest(),
            'messages' => fn ($query) => $query->with('auteur')->oldest(),
        ]);

        return view('proprietaire.tickets.show', [
            'ticket' => $ticket,
            'statutOptions' => StatutTicket::cases(),
        ]);
    }

    public function update(UpdateTicketMaintenanceStatusRequest $request, TicketMaintenance $ticket): RedirectResponse
    {
        $ticket->update([
            'statut' => $request->validated('statut'),
        ]);

        JournalAudit::enregistrer('changement_statut_ticket', $ticket, [
            'statut' => $ticket->statut->value,
        ]);

        return redirect()
            ->route('proprietaire.tickets.show', $ticket)
            ->with('status', 'Le statut du ticket a bien été mis à jour.');
    }
}
