<?php

use App\Models\Locataire;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('proprietaire can list only their locataires with filters', function () {
    $proprietaire = User::factory()->proprietaire()->create();
    $autreProprietaire = User::factory()->proprietaire()->create();

    $locataireActif = Locataire::factory()
        ->for($proprietaire, 'creePar')
        ->create([
            'prenom' => 'Marie',
            'nom' => 'Martin',
            'email' => 'marie@example.test',
        ]);

    $locataireInactif = Locataire::factory()
        ->for($proprietaire, 'creePar')
        ->create([
            'prenom' => 'Paul',
            'nom' => 'Durand',
            'email' => 'paul@example.test',
        ]);

    $locataireInactif->user->update(['is_active' => false]);

    $locataireExterne = Locataire::factory()
        ->for($autreProprietaire, 'creePar')
        ->create([
            'prenom' => 'Luc',
            'nom' => 'Bernard',
        ]);

    $response = $this
        ->actingAs($proprietaire)
        ->get(route('proprietaire.locataires.index', [
            'recherche' => 'marie',
            'statut_compte' => 'actif',
        ]));

    $response->assertSuccessful();
    $response->assertSee($locataireActif->nomComplet());
    $response->assertDontSee($locataireInactif->nomComplet());
    $response->assertDontSee($locataireExterne->nomComplet());
});

test('proprietaire can create a locataire account with temporary credentials', function () {
    $proprietaire = User::factory()->proprietaire()->create();

    $response = $this->actingAs($proprietaire)->post(route('proprietaire.locataires.store'), [
        'prenom' => 'Nadia',
        'nom' => 'Lopez',
        'email' => 'nadia.lopez@example.test',
        'telephone' => '06 01 02 03 04',
    ]);

    $locataire = Locataire::query()
        ->with('user')
        ->firstWhere('email', 'nadia.lopez@example.test');

    $this->assertModelExists($locataire);

    expect($locataire->cree_par_user_id)->toBe($proprietaire->id)
        ->and($locataire->user->email)->toBe('nadia.lopez@example.test')
        ->and($locataire->user->is_active)->toBeTrue()
        ->and($locataire->user->must_change_password)->toBeTrue();

    $response->assertRedirect(route('proprietaire.locataires.show', $locataire));
    $response->assertSessionHas('identifiants_locataire');

    $identifiants = session('identifiants_locataire');

    expect($identifiants['email'])->toBe('nadia.lopez@example.test')
        ->and(Hash::check($identifiants['mot_de_passe_temporaire'], $locataire->user->password))->toBeTrue();
});

test('proprietaire can view a locataire detail page', function () {
    $proprietaire = User::factory()->proprietaire()->create();

    $locataire = Locataire::factory()
        ->for($proprietaire, 'creePar')
        ->create([
            'prenom' => 'Sonia',
            'nom' => 'Bamba',
        ]);

    $response = $this->actingAs($proprietaire)->get(route('proprietaire.locataires.show', $locataire));

    $response->assertSuccessful();
    $response->assertSee('Sonia');
    $response->assertSee('Gestion du compte');
});

test('proprietaire can update a locataire and deactivate the account from the edit form', function () {
    $proprietaire = User::factory()->proprietaire()->create();

    $locataire = Locataire::factory()
        ->for($proprietaire, 'creePar')
        ->create([
            'prenom' => 'Alice',
            'nom' => 'Thomas',
            'email' => 'alice@example.test',
        ]);

    $response = $this->actingAs($proprietaire)->put(route('proprietaire.locataires.update', $locataire), [
        'prenom' => 'Alice',
        'nom' => 'Renard',
        'email' => 'alice.renard@example.test',
        'telephone' => '07 11 22 33 44',
        'is_active' => '0',
    ]);

    $locataire->refresh()->load('user');

    expect($locataire->nom)->toBe('Renard')
        ->and($locataire->email)->toBe('alice.renard@example.test')
        ->and($locataire->telephone)->toBe('07 11 22 33 44')
        ->and($locataire->user->email)->toBe('alice.renard@example.test')
        ->and($locataire->user->is_active)->toBeFalse();

    $response->assertRedirect(route('proprietaire.locataires.show', $locataire));
});

test('proprietaire can toggle locataire account activation', function () {
    $proprietaire = User::factory()->proprietaire()->create();

    $locataire = Locataire::factory()
        ->for($proprietaire, 'creePar')
        ->create();

    $this->actingAs($proprietaire)
        ->patch(route('proprietaire.locataires.activation', $locataire))
        ->assertRedirect();

    expect($locataire->fresh()->user->is_active)->toBeFalse();

    $this->actingAs($proprietaire)
        ->patch(route('proprietaire.locataires.activation', $locataire))
        ->assertRedirect();

    expect($locataire->fresh()->user->is_active)->toBeTrue();
});

test('locataire can not access proprietaire locataires screens', function () {
    $locataire = User::factory()->locataire()->create([
        'must_change_password' => false,
    ]);

    $response = $this->actingAs($locataire)->get(route('proprietaire.locataires.index'));

    $response->assertForbidden();
});

test('proprietaire can not view another proprietaires locataire', function () {
    $proprietaire = User::factory()->proprietaire()->create();
    $autreProprietaire = User::factory()->proprietaire()->create();

    $locataire = Locataire::factory()
        ->for($autreProprietaire, 'creePar')
        ->create();

    $response = $this->actingAs($proprietaire)->get(route('proprietaire.locataires.show', $locataire));

    $response->assertForbidden();
});
