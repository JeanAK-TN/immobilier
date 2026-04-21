<?php

use App\Enums\StatutBien;
use App\Enums\TypeBien;
use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Locataire;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('proprietaire can list and filter only their biens', function () {
    $proprietaire = User::factory()->proprietaire()->create();
    $autreProprietaire = User::factory()->proprietaire()->create();

    $bienOccupe = Bien::factory()
        ->for($proprietaire, 'proprietaire')
        ->create([
            'nom' => 'Résidence Orchidée',
            'type' => TypeBien::Maison,
            'ville' => 'Lomé',
            'statut' => StatutBien::Occupe,
        ]);

    $bienDisponible = Bien::factory()
        ->for($proprietaire, 'proprietaire')
        ->create([
            'nom' => 'Studio Liberté',
            'type' => TypeBien::Appartement,
            'ville' => 'Paris',
            'statut' => StatutBien::Disponible,
        ]);

    $bienExterne = Bien::factory()
        ->for($autreProprietaire, 'proprietaire')
        ->create(['nom' => 'Villa Cachée']);

    $locataire = Locataire::factory()
        ->for($proprietaire, 'creePar')
        ->create();

    Contrat::factory()
        ->for($bienOccupe)
        ->for($locataire)
        ->actif()
        ->create();

    $response = $this
        ->actingAs($proprietaire)
        ->get(route('proprietaire.biens.index', [
            'recherche' => 'Orchidée',
            'type' => TypeBien::Maison->value,
            'occupation' => 'occupe',
        ]));

    $response->assertSuccessful();
    $response->assertSee('Résidence Orchidée');
    $response->assertDontSee('Studio Liberté');
    $response->assertDontSee($bienExterne->nom);
    $response->assertSee('Occupé');
});

test('proprietaire can create a bien with photos', function () {
    Storage::fake('public');

    $proprietaire = User::factory()->proprietaire()->create();

    $response = $this->actingAs($proprietaire)->post(route('proprietaire.biens.store'), [
        'nom' => 'Appartement Horizon',
        'type' => TypeBien::Appartement->value,
        'adresse' => '12 rue des Fleurs',
        'ville' => 'Lyon',
        'pays' => 'France',
        'description' => 'Appartement lumineux avec balcon.',
        'statut' => StatutBien::Disponible->value,
        'photos' => [
            UploadedFile::fake()->create('facade.jpg', 150, 'image/jpeg'),
            UploadedFile::fake()->create('salon.png', 180, 'image/png'),
        ],
    ]);

    $bien = Bien::query()->firstWhere('nom', 'Appartement Horizon');

    $this->assertModelExists($bien);

    expect($bien->user_id)->toBe($proprietaire->id)
        ->and($bien->photos()->count())->toBe(2);

    foreach ($bien->photos as $photo) {
        Storage::disk('public')->assertExists($photo->chemin);
    }

    $response->assertRedirect(route('proprietaire.biens.show', $bien));
});

test('proprietaire can update a bien and remove selected photos', function () {
    Storage::fake('public');

    $proprietaire = User::factory()->proprietaire()->create();

    $bien = Bien::factory()
        ->for($proprietaire, 'proprietaire')
        ->create([
            'nom' => 'Maison à rénover',
            'statut' => StatutBien::Disponible,
        ]);

    $ancienneImage = UploadedFile::fake()->create('ancienne-photo.jpg', 120, 'image/jpeg');
    $ancienChemin = $ancienneImage->store("biens/{$bien->id}/photos", 'public');

    $photoExistante = $bien->photos()->create([
        'uploade_par_user_id' => $proprietaire->id,
        'nom_fichier' => basename($ancienChemin),
        'nom_original' => 'ancienne-photo.jpg',
        'chemin' => $ancienChemin,
        'type_mime' => 'image/jpeg',
        'taille' => Storage::disk('public')->size($ancienChemin),
    ]);

    $response = $this->actingAs($proprietaire)->put(route('proprietaire.biens.update', $bien), [
        'nom' => 'Maison rénovée',
        'type' => TypeBien::Maison->value,
        'adresse' => '18 avenue du Parc',
        'ville' => 'Nantes',
        'pays' => 'France',
        'description' => 'Bien remis à neuf.',
        'statut' => StatutBien::Disponible->value,
        'photos_a_supprimer' => [$photoExistante->id],
        'photos' => [
            UploadedFile::fake()->create('nouvelle-photo.jpg', 160, 'image/jpeg'),
        ],
    ]);

    $bien->refresh();

    expect($bien->nom)->toBe('Maison rénovée')
        ->and($bien->ville)->toBe('Nantes')
        ->and($bien->photos()->count())->toBe(1);

    Storage::disk('public')->assertMissing($ancienChemin);
    Storage::disk('public')->assertExists($bien->photos()->first()->chemin);

    $response->assertRedirect(route('proprietaire.biens.show', $bien));
});

test('proprietaire can view a bien detail page', function () {
    $proprietaire = User::factory()->proprietaire()->create();

    $bien = Bien::factory()
        ->for($proprietaire, 'proprietaire')
        ->create([
            'nom' => 'Villa Panorama',
            'adresse' => '25 corniche des Mers',
            'ville' => 'Nice',
        ]);

    $response = $this->actingAs($proprietaire)->get(route('proprietaire.biens.show', $bien));

    $response->assertSuccessful();
    $response->assertSee('Villa Panorama');
    $response->assertSee('25 corniche des Mers');
    $response->assertSee('Galerie photos');
});

test('proprietaire can delete a bien without contrats and its photos', function () {
    Storage::fake('public');

    $proprietaire = User::factory()->proprietaire()->create();

    $bien = Bien::factory()
        ->for($proprietaire, 'proprietaire')
        ->create();

    $image = UploadedFile::fake()->create('a-supprimer.jpg', 140, 'image/jpeg');
    $chemin = $image->store("biens/{$bien->id}/photos", 'public');

    $bien->photos()->create([
        'uploade_par_user_id' => $proprietaire->id,
        'nom_fichier' => basename($chemin),
        'nom_original' => 'a-supprimer.jpg',
        'chemin' => $chemin,
        'type_mime' => 'image/jpeg',
        'taille' => Storage::disk('public')->size($chemin),
    ]);

    $response = $this->actingAs($proprietaire)->delete(route('proprietaire.biens.destroy', $bien));

    $this->assertModelMissing($bien);
    expect($bien->photos()->count())->toBe(0);
    Storage::disk('public')->assertMissing($chemin);

    $response->assertRedirect(route('proprietaire.biens.index'));
});

test('proprietaire cannot delete a bien linked to a contrat', function () {
    $proprietaire = User::factory()->proprietaire()->create();

    $bien = Bien::factory()
        ->for($proprietaire, 'proprietaire')
        ->create();

    $locataire = Locataire::factory()
        ->for($proprietaire, 'creePar')
        ->create();

    Contrat::factory()
        ->for($bien)
        ->for($locataire)
        ->actif()
        ->create();

    $response = $this->actingAs($proprietaire)->delete(route('proprietaire.biens.destroy', $bien));

    $response->assertForbidden();
    $this->assertModelExists($bien);
});

test('locataire cannot access proprietaire biens screens', function () {
    $locataire = User::factory()->create();

    $response = $this->actingAs($locataire)->get(route('proprietaire.biens.index'));

    $response->assertForbidden();
});

test('a proprietaire cannot view another proprietaires bien', function () {
    $proprietaire = User::factory()->proprietaire()->create();
    $autreProprietaire = User::factory()->proprietaire()->create();

    $bien = Bien::factory()
        ->for($autreProprietaire, 'proprietaire')
        ->create();

    $response = $this->actingAs($proprietaire)->get(route('proprietaire.biens.show', $bien));

    $response->assertForbidden();
});
