<?php

use App\Enums\StatutTicket;
use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Locataire;
use App\Models\MessageTicket;
use App\Models\TicketMaintenance;
use App\Models\User;

test('proprietaire can filter tickets and update their status', function () {
    $proprietaire = User::factory()->proprietaire()->create();
    $autreProprietaire = User::factory()->proprietaire()->create();

    $locataire = Locataire::factory()->for(User::factory()->locataire(), 'user')->for($proprietaire, 'creePar')->create([
        'prenom' => 'Afi',
        'nom' => 'Mensah',
    ]);
    $bien = Bien::factory()->for($proprietaire, 'proprietaire')->create(['nom' => 'Résidence Tokoin']);
    $ticket = TicketMaintenance::factory()
        ->for(Contrat::factory()->for($bien)->for($locataire)->actif())
        ->create([
            'soumis_par_user_id' => $locataire->user_id,
            'titre' => 'Interrupteur cassé',
            'statut' => StatutTicket::Ouvert,
        ]);

    $ticketExterne = TicketMaintenance::factory()
        ->for(Contrat::factory()
            ->for(Bien::factory()->for($autreProprietaire, 'proprietaire'))
            ->for(Locataire::factory()->for(User::factory()->locataire(), 'user')->for($autreProprietaire, 'creePar'))
            ->actif())
        ->create([
            'titre' => 'Ticket externe',
        ]);

    $indexResponse = $this->actingAs($proprietaire)->get(route('proprietaire.tickets.index', [
        'recherche' => 'Tokoin',
        'statut' => StatutTicket::Ouvert->value,
        'locataire_id' => $locataire->id,
        'bien_id' => $bien->id,
    ]));

    $indexResponse->assertSuccessful();
    $indexResponse->assertSee('Interrupteur cassé');
    $indexResponse->assertDontSee('Ticket externe');

    $updateResponse = $this->actingAs($proprietaire)->patch(route('proprietaire.tickets.update', $ticket), [
        'statut' => StatutTicket::EnCours->value,
    ]);

    $updateResponse->assertRedirect(route('proprietaire.tickets.show', $ticket));
    expect($ticket->fresh()->statut)->toBe(StatutTicket::EnCours);
});

test('proprietaire can add an internal note that remains hidden from the locataire', function () {
    $proprietaire = User::factory()->proprietaire()->create(['name' => 'Propriétaire Test']);
    $locataireUser = User::factory()->locataire()->create(['must_change_password' => false]);
    $locataire = Locataire::factory()->for($locataireUser)->for($proprietaire, 'creePar')->create();
    $ticket = TicketMaintenance::factory()
        ->for(Contrat::factory()
            ->for(Bien::factory()->for($proprietaire, 'proprietaire'))
            ->for($locataire)
            ->actif())
        ->create([
            'soumis_par_user_id' => $locataireUser->id,
        ]);

    $response = $this->actingAs($proprietaire)->post(route('proprietaire.tickets.messages.store', $ticket), [
        'message' => 'Vérifier le devis du plombier habituel.',
        'est_note_interne' => '1',
    ]);

    $response->assertRedirect(route('proprietaire.tickets.show', $ticket));

    expect(
        MessageTicket::query()
            ->where('ticket_maintenance_id', $ticket->id)
            ->where('message', 'Vérifier le devis du plombier habituel.')
            ->where('est_note_interne', true)
            ->exists()
    )->toBeTrue();

    $locataireResponse = $this->actingAs($locataireUser)->get(route('locataire.tickets.show', $ticket));

    $locataireResponse->assertSuccessful();
    $locataireResponse->assertDontSee('Vérifier le devis du plombier habituel.');
});

test('proprietaire can not view another proprietaires ticket', function () {
    $proprietaire = User::factory()->proprietaire()->create();
    $autreProprietaire = User::factory()->proprietaire()->create();
    $ticket = TicketMaintenance::factory()
        ->for(Contrat::factory()
            ->for(Bien::factory()->for($autreProprietaire, 'proprietaire'))
            ->for(Locataire::factory()->for(User::factory()->locataire(), 'user')->for($autreProprietaire, 'creePar'))
            ->actif())
        ->create();

    $response = $this->actingAs($proprietaire)->get(route('proprietaire.tickets.show', $ticket));

    $response->assertForbidden();
});
