<?php

use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Locataire;
use App\Models\Paiement;
use App\Models\Quittance;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

test('locataire can list and download their quittances', function () {
    Storage::fake('local');

    $proprietaire = User::factory()->proprietaire()->create();
    $locataireUser = User::factory()->locataire()->create(['must_change_password' => false]);
    $locataire = Locataire::factory()->for($locataireUser)->for($proprietaire, 'creePar')->create();
    $contrat = Contrat::factory()
        ->for(Bien::factory()->for($proprietaire, 'proprietaire')->create(['nom' => 'Appartement Agoe']))
        ->for($locataire)
        ->actif()
        ->create();
    $paiement = Paiement::factory()->for($contrat)->reussi()->create([
        'montant' => 98000,
    ]);
    $quittance = Quittance::factory()->create([
        'contrat_id' => $contrat->id,
        'paiement_id' => $paiement->id,
        'generee_par_user_id' => $proprietaire->id,
        'numero_quittance' => 'QUIT-2026-0007',
        'fichier_path' => 'quittances/quit-2026-0007.pdf',
    ]);

    Storage::disk('local')->put($quittance->fichier_path, '%PDF-1.4');

    $indexResponse = $this->actingAs($locataireUser)->get(route('locataire.quittances.index'));
    $indexResponse->assertSuccessful();
    $indexResponse->assertSee('QUIT-2026-0007');
    $indexResponse->assertSee('Appartement Agoe');

    $downloadResponse = $this->actingAs($locataireUser)->get(route('locataire.quittances.download', $quittance));
    $downloadResponse->assertOk();
    $downloadResponse->assertDownload('quittance-quit-2026-0007.pdf');
});

test('locataire cannot download another locataires quittance', function () {
    Storage::fake('local');

    $proprietaire = User::factory()->proprietaire()->create();
    $locataireUser = User::factory()->locataire()->create(['must_change_password' => false]);
    $autreLocataireUser = User::factory()->locataire()->create(['must_change_password' => false]);
    $autreLocataire = Locataire::factory()->for($autreLocataireUser)->for($proprietaire, 'creePar')->create();
    $contrat = Contrat::factory()
        ->for(Bien::factory()->for($proprietaire, 'proprietaire'))
        ->for($autreLocataire)
        ->actif()
        ->create();
    $paiement = Paiement::factory()->for($contrat)->reussi()->create();
    $quittance = Quittance::factory()->create([
        'contrat_id' => $contrat->id,
        'paiement_id' => $paiement->id,
        'generee_par_user_id' => $proprietaire->id,
        'fichier_path' => 'quittances/quit-2026-0008.pdf',
    ]);

    Storage::disk('local')->put($quittance->fichier_path, '%PDF-1.4');

    $response = $this->actingAs($locataireUser)->get(route('locataire.quittances.download', $quittance));

    $response->assertForbidden();
});
