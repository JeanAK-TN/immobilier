<?php

namespace App\Http\Controllers\Proprietaire;

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

        if (! $estNoteInterne) {
            $ticket->load('contrat.locataire.user');
            $locataireUser = $ticket->contrat->locataire->user;

            if ($locataireUser) {
                $locataireUser->notify(
                    new MessageTicketEnvoyeNotification($ticket, $request->user())
                );
            }
        }

        return redirect()
            ->route('proprietaire.tickets.show', $ticket)
            ->with('status', $estNoteInterne
                ? 'La note interne a bien été ajoutée.'
                : 'La réponse a bien été ajoutée.');
    }
}
