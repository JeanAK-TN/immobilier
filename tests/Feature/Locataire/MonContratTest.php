<?php

use App\Enums\StatutBien;
use App\Enums\StatutContrat;
use App\Models\Bien;
use App\Models\Contrat;
use App\Models\JournalAudit;
use App\Models\Locataire;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

test('locataire can view their current contrat', function () {
    $proprietaire = User::factory()->proprietaire()->create();
    $locataireUser = User::factory()->locataire()->create(['must_change_password' => false]);
    $locataire = Locataire::factory()->for($locataireUser)->for($proprietaire, 'creePar')->create([
        'prenom' => 'Nadia',
        'nom' => 'Lopez',
    ]);
    $bien = Bien::factory()->for($proprietaire, 'proprietaire')->create([
        'nom' => 'Résidence Atlas',
    ]);

    Contrat::factory()
        ->for($bien)
        ->for($locataire)
        ->enAttenteSignature()
        ->create([
            'document_path' => 'contrats/documents/bail-atlas.pdf',
        ]);

    $response = $this->actingAs($locataireUser)->get(route('locataire.contrat.show'));

    $response->assertSuccessful();
    $response->assertSee('Résidence Atlas');
    $response->assertSee('En attente de signature');
    $response->assertSee('Signer le contrat');
    $response->assertSee('FCFA');
});

test('locataire can sign their current contrat and activate the bien', function () {
    Storage::fake('local');

    $proprietaire = User::factory()->proprietaire()->create();
    $locataireUser = User::factory()->locataire()->create(['must_change_password' => false]);
    $locataire = Locataire::factory()->for($locataireUser)->for($proprietaire, 'creePar')->create();
    $bien = Bien::factory()->for($proprietaire, 'proprietaire')->create([
        'statut' => StatutBien::Disponible,
    ]);
    $contrat = Contrat::factory()
        ->for($bien)
        ->for($locataire)
        ->enAttenteSignature()
        ->create([
            'document_path' => 'contrats/documents/bail-signature.pdf',
        ]);

    Storage::disk('local')->put($contrat->document_path, 'pdf');

    $response = $this
        ->actingAs($locataireUser)
        ->withServerVariables(['REMOTE_ADDR' => '203.0.113.9'])
        ->put(route('locataire.contrat.sign'), [
            'confirmation_signature' => '1',
            'signe_nom' => 'Nadia Lopez',
        ]);

    $contrat->refresh();
    $bien->refresh();

    $response->assertRedirect(route('locataire.contrat.show'));

    expect($contrat->statut)->toBe(StatutContrat::Actif)
        ->and($contrat->isSigne())->toBeTrue()
        ->and($contrat->signe_nom)->toBe('Nadia Lopez')
        ->and($contrat->signe_ip)->toBe('203.0.113.9')
        ->and($bien->statut)->toBe(StatutBien::Occupe)
        ->and(
            JournalAudit::query()
                ->where('action', 'signature_contrat_locataire')
                ->where('modele_id', $contrat->id)
                ->exists()
        )->toBeTrue();
});

test('locataire can download their current contrat document', function () {
    Storage::fake('local');

    $proprietaire = User::factory()->proprietaire()->create();
    $locataireUser = User::factory()->locataire()->create(['must_change_password' => false]);
    $locataire = Locataire::factory()->for($locataireUser)->for($proprietaire, 'creePar')->create();
    $bien = Bien::factory()->for($proprietaire, 'proprietaire')->create();
    $contrat = Contrat::factory()
        ->for($bien)
        ->for($locataire)
        ->actif()
        ->create([
            'document_path' => 'contrats/documents/bail-locataire.pdf',
        ]);

    Storage::disk('local')->put($contrat->document_path, 'pdf');

    $response = $this->actingAs($locataireUser)->get(route('locataire.contrat.document'));

    $response->assertOk();
    $response->assertDownload('bail-locataire.pdf');
});

test('locataire sees an empty state when no current contrat is available', function () {
    $proprietaire = User::factory()->proprietaire()->create();
    $locataireUser = User::factory()->locataire()->create(['must_change_password' => false]);

    Locataire::factory()->for($locataireUser)->for($proprietaire, 'creePar')->create();

    $response = $this->actingAs($locataireUser)->get(route('locataire.contrat.show'));

    $response->assertSuccessful();
    $response->assertSee('Aucun contrat à consulter');
});

test('locataire must confirm the signature checkbox before signing', function () {
    Storage::fake('local');

    $proprietaire = User::factory()->proprietaire()->create();
    $locataireUser = User::factory()->locataire()->create(['must_change_password' => false]);
    $locataire = Locataire::factory()->for($locataireUser)->for($proprietaire, 'creePar')->create();
    $bien = Bien::factory()->for($proprietaire, 'proprietaire')->create();
    $contrat = Contrat::factory()
        ->for($bien)
        ->for($locataire)
        ->enAttenteSignature()
        ->create([
            'document_path' => 'contrats/documents/bail-erreur.pdf',
        ]);

    Storage::disk('local')->put($contrat->document_path, 'pdf');

    $response = $this->actingAs($locataireUser)->from(route('locataire.contrat.show'))->put(route('locataire.contrat.sign'), [
        'signe_nom' => 'Nadia Lopez',
    ]);

    $response->assertRedirect(route('locataire.contrat.show'));
    $response->assertSessionHasErrors('confirmation_signature');

    expect($contrat->fresh()->statut)->toBe(StatutContrat::EnAttente)
        ->and($contrat->fresh()->isSigne())->toBeFalse();
});
