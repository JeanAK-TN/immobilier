<?php

namespace App\Http\Controllers\Locataire;

use App\Enums\CategorieTicket;
use App\Enums\PrioriteTicket;
use App\Enums\StatutTicket;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTicketMaintenanceRequest;
use App\Mail\NouveauTicketMail;
use App\Models\JournalAudit;
use App\Models\TicketMaintenance;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class TicketMaintenanceController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', TicketMaintenance::class);

        $recherche = trim((string) $request->query('recherche', ''));
        $statut = trim((string) $request->query('statut', ''));
        $contratActif = $request->user()?->locataire?->contratActif()?->load('bien');
        $ticketsActifsContratCount = $contratActif?->tickets()->actif()->count() ?? 0;
        $ticketsTotalContratCount = $contratActif?->tickets()->count() ?? 0;

        $tickets = TicketMaintenance::query()
            ->pourLocataire($request->user())
            ->with(['contrat.bien'])
            ->withCount([
                'messages' => fn ($query) => $query->where('est_note_interne', false),
                'piecesJointes',
            ])
            ->recherche($recherche)
            ->when($statut !== '', fn ($query) => $query->where('statut', $statut))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('locataire.tickets.index', [
            'contratActif' => $contratActif,
            'ticketsActifsContratCount' => $ticketsActifsContratCount,
            'ticketsTotalContratCount' => $ticketsTotalContratCount,
            'tickets' => $tickets,
            'categorieOptions' => CategorieTicket::cases(),
            'prioriteOptions' => PrioriteTicket::cases(),
            'statutOptions' => StatutTicket::cases(),
            'filtres' => compact('recherche', 'statut'),
        ]);
    }

    public function store(StoreTicketMaintenanceRequest $request): RedirectResponse
    {
        $contrat = $request->user()?->locataire?->contratActif();

        abort_unless($contrat !== null, 403);

        $ticket = DB::transaction(function () use ($request, $contrat): TicketMaintenance {
            $ticket = TicketMaintenance::create([
                'contrat_id' => $contrat->id,
                'soumis_par_user_id' => $request->user()->id,
                'titre' => $request->validated('titre'),
                'categorie' => $request->validated('categorie'),
                'priorite' => $request->validated('priorite'),
                'description' => $request->validated('description'),
                'statut' => StatutTicket::Ouvert,
            ]);

            $this->enregistrerPhotos($ticket, $request->file('photos', []), $request->user());

            JournalAudit::enregistrer('creation_ticket_maintenance', $ticket, [
                'contrat_id' => $ticket->contrat_id,
                'statut' => $ticket->statut->value,
            ]);

            return $ticket;
        });

        $ticket->load('contrat.bien.proprietaire', 'contrat.locataire');

        Mail::to($ticket->contrat->bien->proprietaire->email)
            ->send(new NouveauTicketMail($ticket));

        return redirect()
            ->route('locataire.tickets.show', $ticket)
            ->with('status', 'Le ticket de maintenance a bien été créé.');
    }

    public function show(Request $request, TicketMaintenance $ticket): View
    {
        $this->authorize('view', $ticket);

        $ticket->load([
            'contrat.bien',
            'piecesJointes' => fn ($query) => $query->latest(),
            'messages' => fn ($query) => $query
                ->where('est_note_interne', false)
                ->with('auteur')
                ->oldest(),
        ]);

        return view('locataire.tickets.show', [
            'ticket' => $ticket,
        ]);
    }

    /**
     * @param  array<int, UploadedFile>  $photos
     */
    private function enregistrerPhotos(TicketMaintenance $ticket, array $photos, User $user): void
    {
        foreach ($photos as $photo) {
            $chemin = $photo->store("tickets/{$ticket->id}/photos", 'public');

            $ticket->piecesJointes()->create([
                'uploade_par_user_id' => $user->id,
                'nom_fichier' => basename($chemin),
                'nom_original' => $photo->getClientOriginalName(),
                'chemin' => $chemin,
                'type_mime' => $photo->getMimeType() ?? 'application/octet-stream',
                'taille' => $photo->getSize(),
            ]);
        }
    }
}
