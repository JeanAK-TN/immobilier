<?php

use App\Enums\OperateurMobileMoney;
use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Locataire;
use App\Models\Paiement;
use App\Models\Quittance;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

test('proprietaire can generate a quittance pdf from a successful paiement', function () {
    Storage::fake('local');

    $proprietaire = User::factory()->proprietaire()->create();
    $locataire = Locataire::factory()->for($proprietaire, 'creePar')->create();
    $contrat = Contrat::factory()
        ->for(Bien::factory()->for($proprietaire, 'proprietaire')->state([
            'nom' => 'Villa Tokoin',
            'ville' => 'Lomé',
            'pays' => 'Togo',
        ]))
        ->for($locataire)
        ->actif()
        ->create();

    $paiement = Paiement::factory()
        ->for($contrat)
        ->mobileMoney(OperateurMobileMoney::MixxTMoney)
        ->reussi()
        ->create([
            'periode_annee' => 2026,
            'periode_mois' => 5,
            'montant' => 125000,
        ]);

    $response = $this->actingAs($proprietaire)->post(route('proprietaire.quittances.store', $paiement));

    $quittance = Quittance::query()->latest('id')->first();

    $this->assertModelExists($quittance);
    expect($quittance->numero_quittance)->toStartWith('QUIT-')
        ->and($quittance->paiement_id)->toBe($paiement->id)
        ->and($quittance->contrat_id)->toBe($contrat->id)
        ->and($quittance->documentDisponible())->toBeTrue();

    Storage::disk('local')->assertExists($quittance->fichier_path);

    $pdfContent = Storage::disk('local')->get($quittance->fichier_path);

    expect($pdfContent)
        ->toStartWith('%PDF-1.4')
        ->toContain('/Encoding /WinAnsiEncoding')
        ->toContain(iconv('UTF-8', 'Windows-1252//TRANSLIT//IGNORE', 'Lomé'));

    $response->assertRedirect(route('proprietaire.paiements.show', $paiement));
});

test('proprietaire sees generated quittances in history screens', function () {
    Storage::fake('local');

    $proprietaire = User::factory()->proprietaire()->create();
    $locataire = Locataire::factory()->for($proprietaire, 'creePar')->create(['prenom' => 'Afi', 'nom' => 'Mensah']);
    $contrat = Contrat::factory()
        ->for(Bien::factory()->for($proprietaire, 'proprietaire')->create(['nom' => 'Résidence Tokoin']))
        ->for($locataire)
        ->actif()
        ->create();
    $paiement = Paiement::factory()->for($contrat)->reussi()->create([
        'periode_annee' => 2026,
        'periode_mois' => 6,
    ]);

    $quittance = Quittance::factory()->create([
        'contrat_id' => $contrat->id,
        'paiement_id' => $paiement->id,
        'generee_par_user_id' => $proprietaire->id,
        'periode_mois' => 6,
        'periode_annee' => 2026,
        'numero_quittance' => 'QUIT-2026-0001',
        'fichier_path' => 'quittances/quit-2026-0001.pdf',
    ]);

    Storage::disk('local')->put($quittance->fichier_path, '%PDF-1.4');

    $indexResponse = $this->actingAs($proprietaire)->get(route('proprietaire.quittances.index', [
        'contrat_id' => $contrat->id,
        'periode' => '2026-06',
    ]));

    $indexResponse->assertSuccessful();
    $indexResponse->assertSee('QUIT-2026-0001');
    $indexResponse->assertSee('Résidence Tokoin');

    $contratResponse = $this->actingAs($proprietaire)->get(route('proprietaire.contrats.show', $contrat));

    $contratResponse->assertSuccessful();
    $contratResponse->assertSee('Historique des quittances');
    $contratResponse->assertSee('QUIT-2026-0001');
});

test('proprietaire cannot generate a quittance for a failed paiement', function () {
    $proprietaire = User::factory()->proprietaire()->create();
    $locataire = Locataire::factory()->for($proprietaire, 'creePar')->create();
    $contrat = Contrat::factory()
        ->for(Bien::factory()->for($proprietaire, 'proprietaire'))
        ->for($locataire)
        ->actif()
        ->create();
    $paiement = Paiement::factory()->for($contrat)->echoue()->create();

    $response = $this->actingAs($proprietaire)->post(route('proprietaire.quittances.store', $paiement));

    $response->assertForbidden();
    expect(Quittance::query()->count())->toBe(0);
});
