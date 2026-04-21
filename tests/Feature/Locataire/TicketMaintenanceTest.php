<?php

use App\Enums\StatutTicket;
use App\Models\Bien;
use App\Models\Contrat;
use App\Models\JournalAudit;
use App\Models\Locataire;
use App\Models\MessageTicket;
use App\Models\TicketMaintenance;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('locataire can create a maintenance ticket with photos', function () {
    Storage::fake('public');

    $proprietaire = User::factory()->proprietaire()->create();
    $locataireUser = User::factory()->locataire()->create(['must_change_password' => false]);
    $locataire = Locataire::factory()->for($locataireUser)->for($proprietaire, 'creePar')->create();

    Contrat::factory()
        ->for(Bien::factory()->for($proprietaire, 'proprietaire')->create(['nom' => 'Villa Ablogamé']))
        ->for($locataire)
        ->actif()
        ->create();

    $response = $this->actingAs($locataireUser)->post(route('locataire.tickets.store'), [
        'titre' => 'Fuite sous l’évier',
        'categorie' => 'plomberie',
        'priorite' => 'haute',
        'description' => 'Une fuite apparaît sous l’évier de la cuisine depuis ce matin.',
        'photos' => [
            UploadedFile::fake()->createWithContent(
                'fuite.png',
                base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAusB9sY9qs0AAAAASUVORK5CYII=')
            ),
        ],
    ]);

    $ticket = TicketMaintenance::query()->with('piecesJointes')->latest('id')->first();

    $this->assertModelExists($ticket);

    expect($ticket->statut)->toBe(StatutTicket::Ouvert)
        ->and($ticket->soumis_par_user_id)->toBe($locataireUser->id)
        ->and($ticket->piecesJointes)->toHaveCount(1)
        ->and(
            JournalAudit::query()
                ->where('action', 'creation_ticket_maintenance')
                ->where('modele_id', $ticket->id)
                ->exists()
        )->toBeTrue();

    Storage::disk('public')->assertExists($ticket->piecesJointes->first()->chemin);

    $response->assertRedirect(route('locataire.tickets.show', $ticket));
});

test('locataire can view their ticket thread and reply without seeing internal notes', function () {
    $proprietaire = User::factory()->proprietaire()->create(['name' => 'Bailleur Test']);
    $locataireUser = User::factory()->locataire()->create(['must_change_password' => false]);
    $locataire = Locataire::factory()->for($locataireUser)->for($proprietaire, 'creePar')->create();
    $ticket = TicketMaintenance::factory()
        ->for(Contrat::factory()
            ->for(Bien::factory()->for($proprietaire, 'proprietaire'))
            ->for($locataire)
            ->actif())
        ->create([
            'soumis_par_user_id' => $locataireUser->id,
            'titre' => 'Climatisation en panne',
        ]);

    MessageTicket::query()->create([
        'ticket_maintenance_id' => $ticket->id,
        'user_id' => $proprietaire->id,
        'message' => 'Nous envoyons un technicien demain matin.',
        'est_note_interne' => false,
    ]);

    MessageTicket::query()->create([
        'ticket_maintenance_id' => $ticket->id,
        'user_id' => $proprietaire->id,
        'message' => 'Prévoir un prestataire partenaire.',
        'est_note_interne' => true,
    ]);

    $response = $this->actingAs($locataireUser)->get(route('locataire.tickets.show', $ticket));

    $response->assertSuccessful();
    $response->assertSee('Climatisation en panne');
    $response->assertSee('Nous envoyons un technicien demain matin.');
    $response->assertDontSee('Prévoir un prestataire partenaire.');

    $replyResponse = $this->actingAs($locataireUser)->post(route('locataire.tickets.messages.store', $ticket), [
        'message' => 'Merci, je serai présent demain.',
    ]);

    $replyResponse->assertRedirect(route('locataire.tickets.show', $ticket));

    expect(
        MessageTicket::query()
            ->where('ticket_maintenance_id', $ticket->id)
            ->where('user_id', $locataireUser->id)
            ->where('message', 'Merci, je serai présent demain.')
            ->exists()
    )->toBeTrue();
});

test('locataire without active contrat can not create a ticket', function () {
    $proprietaire = User::factory()->proprietaire()->create();
    $locataireUser = User::factory()->locataire()->create(['must_change_password' => false]);

    Locataire::factory()->for($locataireUser)->for($proprietaire, 'creePar')->create();

    $response = $this->actingAs($locataireUser)->post(route('locataire.tickets.store'), [
        'titre' => 'Panne',
        'categorie' => 'autre',
        'priorite' => 'moyenne',
        'description' => 'Le problème survient sans contrat actif.',
    ]);

    $response->assertForbidden();
});

test('locataire sees validation errors for invalid ticket photos without crashing the page', function () {
    $proprietaire = User::factory()->proprietaire()->create();
    $locataireUser = User::factory()->locataire()->create(['must_change_password' => false]);
    $locataire = Locataire::factory()->for($locataireUser)->for($proprietaire, 'creePar')->create();

    Contrat::factory()
        ->for(Bien::factory()->for($proprietaire, 'proprietaire'))
        ->for($locataire)
        ->actif()
        ->create();

    $response = $this
        ->actingAs($locataireUser)
        ->from(route('locataire.tickets.index'))
        ->followingRedirects()
        ->post(route('locataire.tickets.store'), [
            'titre' => 'Problème de portail',
            'categorie' => 'autre',
            'priorite' => 'moyenne',
            'description' => 'Le portail se bloque régulièrement en fin de course.',
            'photos' => [
                UploadedFile::fake()->create('preuve.pdf', 50, 'application/pdf'),
            ],
        ]);

    $response->assertSuccessful();
    $response->assertSee('Chaque pièce jointe doit être une image valide.');
});
