<?php

namespace App\Http\Controllers\Locataire;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTicketMessageRequest;
use App\Models\JournalAudit;
use App\Models\TicketMaintenance;
use App\Notifications\MessageTicketEnvoyeNotification;
use Illuminate\Http\RedirectResponse;

class TicketMessageController extends Controller
{
    public function store(StoreTicketMessageRequest $request, TicketMaintenance $ticket): RedirectResponse
    {
        $ticket->messages()->create([
            'user_id' => $request->user()->id,
            'message' => $request->validated('message'),
            'est_note_interne' => false,
        ]);

        JournalAudit::enregistrer('ajout_message_ticket', $ticket, [
            'ticket_id' => $ticket->id,
            'est_note_interne' => false,
        ]);

        $ticket->load('contrat.bien.proprietaire');
        $ticket->contrat->bien->proprietaire->notify(
            new MessageTicketEnvoyeNotification($ticket, $request->user())
        );

        return redirect()
            ->route('locataire.tickets.show', $ticket)
            ->with('status', 'Votre message a bien été ajouté.');
    }
}
