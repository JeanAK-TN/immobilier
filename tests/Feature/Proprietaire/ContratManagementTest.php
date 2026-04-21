<?php

use App\Enums\StatutBien;
use App\Enums\StatutContrat;
use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Locataire;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('proprietaire can list and filter only their contrats', function () {
    $proprietaire = User::factory()->proprietaire()->create();
    $autreProprietaire = User::factory()->proprietaire()->create();

    $bien = Bien::factory()->for($proprietaire, 'proprietaire')->create(['nom' => 'Appartement Central']);
    $locataire = Locataire::factory()->for($proprietaire, 'creePar')->create(['prenom' => 'Sarah', 'nom' => 'Mendy']);

    $contratAffiche = Contrat::factory()
        ->for($bien)
        ->for($locataire)
        ->enAttenteSignature()
        ->create();

    $contratMasque = Contrat::factory()
        ->for(Bien::factory()->for($proprietaire, 'proprietaire'))
        ->for(Locataire::factory()->for($proprietaire, 'creePar'))
        ->actif()
        ->create();

    $contratExterne = Contrat::factory()
        ->for(Bien::factory()->for($autreProprietaire, 'proprietaire'))
        ->for(Locataire::factory()->for($autreProprietaire, 'creePar'))
        ->create();

    $response = $this
        ->actingAs($proprietaire)
        ->get(route('proprietaire.contrats.index', [
            'recherche' => 'Central',
            'statut' => StatutContrat::EnAttente->value,
            'bien_id' => $bien->id,
            'locataire_id' => $locataire->id,
        ]));

    $response->assertSuccessful();
    $response->assertSee($bien->nom);
    $response->assertSee($locataire->nomComplet());
    $response->assertDontSee(route('proprietaire.contrats.show', $contratMasque));
    $response->assertDontSee(route('proprietaire.contrats.show', $contratExterne));
    $response->assertSee(StatutContrat::EnAttente->label());
});

test('proprietaire can create a contrat with a pdf document', function () {
    Storage::fake('local');

    $proprietaire = User::factory()->proprietaire()->create();
    $bien = Bien::factory()->for($proprietaire, 'proprietaire')->create(['statut' => StatutBien::Disponible]);
    $locataire = Locataire::factory()->for($proprietaire, 'creePar')->create();

    $response = $this->actingAs($proprietaire)->post(route('proprietaire.contrats.store'), [
        'bien_id' => $bien->id,
        'locataire_id' => $locataire->id,
        'date_debut' => '2026-05-01',
        'date_fin' => '2027-04-30',
        'reconduction_auto' => '1',
        'loyer_mensuel' => '950.00',
        'depot_garantie' => '950.00',
        'charges' => '75.00',
        'jour_paiement' => '5',
        'statut' => StatutContrat::EnAttente->value,
        'document_pdf' => UploadedFile::fake()->create('bail.pdf', 300, 'application/pdf'),
    ]);

    $contrat = Contrat::query()->first();

    $this->assertModelExists($contrat);
    expect($contrat->document_path)->not->toBeNull()
        ->and($contrat->statut)->toBe(StatutContrat::EnAttente);

    Storage::disk('local')->assertExists($contrat->document_path);
    $response->assertRedirect(route('proprietaire.contrats.show', $contrat));
});

test('proprietaire can view a contrat detail page', function () {
    $proprietaire = User::factory()->proprietaire()->create();
    $contrat = Contrat::factory()
        ->for(Bien::factory()->for($proprietaire, 'proprietaire'))
        ->for(Locataire::factory()->for($proprietaire, 'creePar'))
        ->actif()
        ->create();

    $response = $this->actingAs($proprietaire)->get(route('proprietaire.contrats.show', $contrat));

    $response->assertSuccessful();
    $response->assertSee('Conditions financières');
    $response->assertSee($contrat->bien->nom);
    $response->assertSee($contrat->locataire->nomComplet());
    $response->assertSee('FCFA');
});

test('proprietaire can update a contrat and sync bien occupancy', function () {
    $proprietaire = User::factory()->proprietaire()->create();
    $bien = Bien::factory()->for($proprietaire, 'proprietaire')->create(['statut' => StatutBien::Occupe]);
    $locataire = Locataire::factory()->for($proprietaire, 'creePar')->create();

    $contrat = Contrat::factory()
        ->for($bien)
        ->for($locataire)
        ->actif()
        ->create([
            'date_fin' => null,
        ]);

    $response = $this->actingAs($proprietaire)->put(route('proprietaire.contrats.update', $contrat), [
        'bien_id' => $bien->id,
        'locataire_id' => $locataire->id,
        'date_debut' => $contrat->date_debut->format('Y-m-d'),
        'date_fin' => '2026-12-31',
        'reconduction_auto' => '0',
        'loyer_mensuel' => '1020.00',
        'depot_garantie' => '1020.00',
        'charges' => '90.00',
        'jour_paiement' => '10',
        'statut' => StatutContrat::Resilie->value,
    ]);

    $contrat->refresh();
    $bien->refresh();

    expect($contrat->statut)->toBe(StatutContrat::Resilie)
        ->and($bien->statut)->toBe(StatutBien::Disponible);

    $response->assertRedirect(route('proprietaire.contrats.show', $contrat));
});

test('proprietaire can download a contrat pdf document', function () {
    Storage::fake('local');

    $proprietaire = User::factory()->proprietaire()->create();
    $contrat = Contrat::factory()
        ->for(Bien::factory()->for($proprietaire, 'proprietaire'))
        ->for(Locataire::factory()->for($proprietaire, 'creePar'))
        ->create([
            'document_path' => 'contrats/documents/contrat-test.pdf',
        ]);

    Storage::disk('local')->put($contrat->document_path, 'pdf');

    $response = $this->actingAs($proprietaire)->get(route('proprietaire.contrats.document', $contrat));

    $response->assertOk();
});

test('proprietaire cannot create a second active contrat for the same bien', function () {
    $proprietaire = User::factory()->proprietaire()->create();
    $bien = Bien::factory()->for($proprietaire, 'proprietaire')->create();
    $locataire1 = Locataire::factory()->for($proprietaire, 'creePar')->create();
    $locataire2 = Locataire::factory()->for($proprietaire, 'creePar')->create();

    Contrat::factory()
        ->for($bien)
        ->for($locataire1)
        ->actif()
        ->create();

    $response = $this->actingAs($proprietaire)->post(route('proprietaire.contrats.store'), [
        'bien_id' => $bien->id,
        'locataire_id' => $locataire2->id,
        'date_debut' => '2026-06-01',
        'date_fin' => '2027-05-31',
        'reconduction_auto' => '0',
        'loyer_mensuel' => '800.00',
        'depot_garantie' => '800.00',
        'charges' => '50.00',
        'jour_paiement' => '5',
        'statut' => StatutContrat::Actif->value,
    ]);

    $response->assertSessionHasErrors('bien_id');
    expect(Contrat::query()->count())->toBe(1);
});

test('locataire cannot access proprietaire contrats screens', function () {
    $locataireUser = User::factory()->locataire()->create([
        'must_change_password' => false,
    ]);

    $response = $this->actingAs($locataireUser)->get(route('proprietaire.contrats.index'));

    $response->assertForbidden();
});

test('proprietaire cannot view another proprietaires contrat', function () {
    $proprietaire = User::factory()->proprietaire()->create();
    $autreProprietaire = User::factory()->proprietaire()->create();

    $contrat = Contrat::factory()
        ->for(Bien::factory()->for($autreProprietaire, 'proprietaire'))
        ->for(Locataire::factory()->for($autreProprietaire, 'creePar'))
        ->create();

    $response = $this->actingAs($proprietaire)->get(route('proprietaire.contrats.show', $contrat));

    $response->assertForbidden();
});
