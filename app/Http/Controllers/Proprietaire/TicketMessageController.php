<?php

namespace App\Http\Controllers\Proprietaire;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTicketMessageRequest;
use App\Models\JournalAudit;
use App\Models\TicketMaintenance;
use Illuminate\Http\RedirectResponse;

class TicketMessageController extends Controller
{
    public function store(StoreTicketMessageRequest $request, TicketMaintenance $ticket): RedirectResponse
    {
        $estNoteInterne = (bool) $request->boolean('est_note_interne');

        $ticket->messages()->create([
            'user_id' => $request->user()->id,
            'message' => $request->validated('message'),
            'est_note_interne' => $estNoteInterne,
        ]);

        JournalAudit::enregistrer($estNoteInterne ? 'ajout_note_interne_ticket' : 'ajout_message_ticket', $ticket, [
            'ticket_id' => $ticket->id,
            'est_note_interne' => $estNoteInterne,
        ]);

        return redirect()
            ->route('proprietaire.tickets.show', $ticket)
            ->with('status', $estNoteInterne
                ? 'La note interne a bien ete ajoutee.'
                : 'La reponse a bien ete ajoutee.');
    }
}
