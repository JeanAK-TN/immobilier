<?php

use App\Enums\StatutContrat;
use App\Enums\StatutTicket;
use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Locataire;
use App\Models\Paiement;
use App\Models\TicketMaintenance;
use App\Models\User;
use App\Notifications\ContratAttribueNotification;
use App\Notifications\ContratSigneNotification;
use App\Notifications\MessageTicketEnvoyeNotification;
use App\Notifications\PaiementEnregistreNotification;
use App\Notifications\QuittanceGenereeNotification;
use App\Notifications\TicketCreeNotification;
use App\Notifications\TicketStatutChangeNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

/**
 * Helper qui crée un trio (proprietaire user, locataire user, locataire model) et un contrat actif lié à un bien.
 *
 * @return array{proprietaire: User, locataireUser: User, locataire: Locataire, bien: Bien, contrat: Contrat}
 */
function buildScenario(array $contratAttrs = []): array
{
    $proprietaire = User::factory()->proprietaire()->create(['must_change_password' => false]);
    $locataireUser = User::factory()->locataire()->create(['must_change_password' => false]);
    $locataire = Locataire::factory()->for($locataireUser)->for($proprietaire, 'creePar')->create();
    $bien = Bien::factory()->for($proprietaire, 'proprietaire')->create();
    $contrat = Contrat::factory()
        ->for($bien)
        ->for($locataire)
        ->actif()
        ->create($contratAttrs);

    return compact('proprietaire', 'locataireUser', 'locataire', 'bien', 'contrat');
}

it('notifies proprietaire when locataire creates a ticket', function () {
    Notification::fake();
    $s = buildScenario();

    $this->actingAs($s['locataireUser'])->post(route('locataire.tickets.store'), [
        'titre' => 'Test fuite',
        'categorie' => 'plomberie',
        'priorite' => 'haute',
        'description' => 'Une fuite au plafond.',
    ]);

    Notification::assertSentTo($s['proprietaire'], TicketCreeNotification::class);
    Notification::assertNotSentTo($s['locataireUser'], TicketCreeNotification::class);
});

it('notifies proprietaire when locataire signs a contrat', function () {
    Notification::fake();
    Storage::fake('local');

    $proprietaire = User::factory()->proprietaire()->create(['must_change_password' => false]);
    $locataireUser = User::factory()->locataire()->create(['must_change_password' => false]);
    $locataire = Locataire::factory()->for($locataireUser)->for($proprietaire, 'creePar')->create();
    $bien = Bien::factory()->for($proprietaire, 'proprietaire')->create();

    $contratEnAttente = Contrat::factory()
        ->for($bien)
        ->for($locataire)
        ->state(['statut' => StatutContrat::EnAttente, 'document_path' => 'fake/contrat.pdf'])
        ->create();

    Storage::disk('local')->put($contratEnAttente->document_path, 'fake-pdf-content');

    $this->actingAs($locataireUser)->put(route('locataire.contrat.sign'), [
        'signe_nom' => 'Jean Test',
        'confirmation_signature' => '1',
    ]);

    Notification::assertSentTo($proprietaire, ContratSigneNotification::class);
});

it('notifies proprietaire when locataire registers a payment', function () {
    Notification::fake();
    $s = buildScenario();

    $this->actingAs($s['locataireUser'])->post(route('locataire.paiements.store'), [
        'periode' => now()->format('Y-m'),
        'montant' => 150000,
        'mode' => 'especes',
    ]);

    Notification::assertSentTo($s['proprietaire'], PaiementEnregistreNotification::class);
});

it('notifies the other party when a public ticket message is posted', function () {
    Notification::fake();
    $s = buildScenario();

    $ticket = TicketMaintenance::factory()->for($s['contrat'])->create([
        'soumis_par_user_id' => $s['locataireUser']->id,
    ]);

    $this->actingAs($s['locataireUser'])->post(route('locataire.tickets.messages.store', $ticket), [
        'message' => 'Une mise à jour de ma part.',
    ]);

    Notification::assertSentTo($s['proprietaire'], MessageTicketEnvoyeNotification::class);

    $this->actingAs($s['proprietaire'])->post(route('proprietaire.tickets.messages.store', $ticket), [
        'message' => 'Bien reçu.',
    ]);

    Notification::assertSentTo($s['locataireUser'], MessageTicketEnvoyeNotification::class);
});

it('does not notify locataire when proprietaire posts an internal note', function () {
    Notification::fake();
    $s = buildScenario();

    $ticket = TicketMaintenance::factory()->for($s['contrat'])->create([
        'soumis_par_user_id' => $s['locataireUser']->id,
    ]);

    $this->actingAs($s['proprietaire'])->post(route('proprietaire.tickets.messages.store', $ticket), [
        'message' => 'Note interne — à ne pas diffuser.',
        'est_note_interne' => '1',
    ]);

    Notification::assertNotSentTo($s['locataireUser'], MessageTicketEnvoyeNotification::class);
});

it('notifies locataire when proprietaire creates a contrat for them', function () {
    Notification::fake();
    $s = buildScenario();

    $autreBien = Bien::factory()->for($s['proprietaire'], 'proprietaire')->create();

    $this->actingAs($s['proprietaire'])->post(route('proprietaire.contrats.store'), [
        'bien_id' => $autreBien->id,
        'locataire_id' => $s['locataire']->id,
        'date_debut' => now()->format('Y-m-d'),
        'jour_paiement' => 5,
        'loyer_mensuel' => 100000,
        'charges' => 0,
        'depot_garantie' => 0,
        'reconduction_auto' => '0',
        'statut' => StatutContrat::Brouillon->value,
    ]);

    Notification::assertSentTo($s['locataireUser'], ContratAttribueNotification::class);
});

it('notifies locataire when proprietaire changes a ticket status', function () {
    Notification::fake();
    $s = buildScenario();

    $ticket = TicketMaintenance::factory()->for($s['contrat'])->create([
        'soumis_par_user_id' => $s['locataireUser']->id,
        'statut' => StatutTicket::Ouvert,
    ]);

    $this->actingAs($s['proprietaire'])->patch(route('proprietaire.tickets.update', $ticket), [
        'statut' => StatutTicket::EnCours->value,
    ]);

    Notification::assertSentTo($s['locataireUser'], TicketStatutChangeNotification::class);
});

it('does not notify locataire when ticket status update keeps the same status', function () {
    Notification::fake();
    $s = buildScenario();

    $ticket = TicketMaintenance::factory()->for($s['contrat'])->create([
        'soumis_par_user_id' => $s['locataireUser']->id,
        'statut' => StatutTicket::Ouvert,
    ]);

    $this->actingAs($s['proprietaire'])->patch(route('proprietaire.tickets.update', $ticket), [
        'statut' => StatutTicket::Ouvert->value,
    ]);

    Notification::assertNotSentTo($s['locataireUser'], TicketStatutChangeNotification::class);
});

it('notifies locataire when proprietaire generates a quittance', function () {
    Notification::fake();
    Storage::fake('local');
    $s = buildScenario();

    $paiement = Paiement::factory()
        ->for($s['contrat'])
        ->reussi()
        ->create([
            'periode_mois' => now()->month,
            'periode_annee' => now()->year,
        ]);

    $this->actingAs($s['proprietaire'])->post(route('proprietaire.quittances.store', $paiement));

    Notification::assertSentTo($s['locataireUser'], QuittanceGenereeNotification::class);
});

it('lists notifications for the authenticated user', function () {
    $s = buildScenario();
    $ticket = TicketMaintenance::factory()->for($s['contrat'])->create([
        'soumis_par_user_id' => $s['locataireUser']->id,
    ]);
    $s['proprietaire']->notify(new TicketCreeNotification($ticket));

    $response = $this->actingAs($s['proprietaire'])->get(route('notifications.index'));

    $response->assertSuccessful();
    $response->assertSee($ticket->titre);
});

it('marks a single notification as read and redirects to its url', function () {
    $s = buildScenario();
    $ticket = TicketMaintenance::factory()->for($s['contrat'])->create([
        'soumis_par_user_id' => $s['locataireUser']->id,
    ]);
    $s['proprietaire']->notify(new TicketCreeNotification($ticket));
    $notification = $s['proprietaire']->notifications()->first();

    $response = $this->actingAs($s['proprietaire'])->post(route('notifications.read', $notification->id));

    $response->assertRedirect(route('proprietaire.tickets.show', $ticket));
    expect($s['proprietaire']->fresh()->unreadNotifications()->count())->toBe(0);
});

it('forbids marking a notification belonging to someone else', function () {
    $s = buildScenario();
    $autreUser = User::factory()->proprietaire()->create(['must_change_password' => false]);
    $ticket = TicketMaintenance::factory()->for($s['contrat'])->create([
        'soumis_par_user_id' => $s['locataireUser']->id,
    ]);
    $s['proprietaire']->notify(new TicketCreeNotification($ticket));
    $notification = $s['proprietaire']->notifications()->first();

    $this->actingAs($autreUser)
        ->post(route('notifications.read', $notification->id))
        ->assertForbidden();

    expect($s['proprietaire']->fresh()->unreadNotifications()->count())->toBe(1);
});

it('marks all notifications as read at once', function () {
    $s = buildScenario();
    $ticket = TicketMaintenance::factory()->for($s['contrat'])->create([
        'soumis_par_user_id' => $s['locataireUser']->id,
    ]);
    $s['proprietaire']->notify(new TicketCreeNotification($ticket));
    $s['proprietaire']->notify(new TicketCreeNotification($ticket));

    expect($s['proprietaire']->fresh()->unreadNotifications()->count())->toBe(2);

    $response = $this->actingAs($s['proprietaire'])->post(route('notifications.read-all'));

    $response->assertRedirect();
    expect($s['proprietaire']->fresh()->unreadNotifications()->count())->toBe(0);
});
