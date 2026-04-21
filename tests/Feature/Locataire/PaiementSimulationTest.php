<?php

use App\Enums\ModePaiement;
use App\Enums\OperateurMobileMoney;
use App\Enums\StatutPaiement;
use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Locataire;
use App\Models\Paiement;
use App\Models\User;

test('locataire can view the simulated paiement page with mobile money options', function () {
    $proprietaire = User::factory()->proprietaire()->create();
    $locataireUser = User::factory()->locataire()->create(['must_change_password' => false]);
    $locataire = Locataire::factory()->for($locataireUser)->for($proprietaire, 'creePar')->create();

    Contrat::factory()
        ->for(Bien::factory()->for($proprietaire, 'proprietaire'))
        ->for($locataire)
        ->actif()
        ->create();

    $response = $this->actingAs($locataireUser)->get(route('locataire.paiements.index'));

    $response->assertSuccessful();
    $response->assertSee('Paiement simulé - aucune transaction réelle');
    $response->assertSee('Mixx/TMoney');
    $response->assertSee('Moov');
    $response->assertSee('Valider le paiement');
});

test('locataire can simulate a successful paiement with Mixx TMoney', function () {
    $proprietaire = User::factory()->proprietaire()->create();
    $locataireUser = User::factory()->locataire()->create(['must_change_password' => false]);
    $locataire = Locataire::factory()->for($locataireUser)->for($proprietaire, 'creePar')->create();

    Contrat::factory()
        ->for(Bien::factory()->for($proprietaire, 'proprietaire'))
        ->for($locataire)
        ->actif()
        ->create();

    $response = $this->actingAs($locataireUser)->post(route('locataire.paiements.store'), [
        'periode' => '2026-05',
        'montant' => '125000',
        'mode' => ModePaiement::MobileMoney->value,
        'operateur_mobile_money' => OperateurMobileMoney::MixxTMoney->value,
    ]);

    $paiement = Paiement::query()->latest('id')->first();

    $this->assertModelExists($paiement);

    expect($paiement->statut)->toBe(StatutPaiement::SimuleReussi)
        ->and($paiement->mode)->toBe(ModePaiement::MobileMoney)
        ->and($paiement->operateur_mobile_money)->toBe(OperateurMobileMoney::MixxTMoney)
        ->and($paiement->reference)->toStartWith('SIM-TG-')
        ->and($paiement->notes)->toBe('Paiement simulé - aucune transaction réelle.');

    $response->assertRedirect(route('locataire.paiements.show', $paiement));
});

test('locataire can simulate a paiement with moov and receives a duplicate warning for the same period', function () {
    $proprietaire = User::factory()->proprietaire()->create();
    $locataireUser = User::factory()->locataire()->create(['must_change_password' => false]);
    $locataire = Locataire::factory()->for($locataireUser)->for($proprietaire, 'creePar')->create();
    $contrat = Contrat::factory()
        ->for(Bien::factory()->for($proprietaire, 'proprietaire'))
        ->for($locataire)
        ->actif()
        ->create();

    Paiement::factory()
        ->for($contrat)
        ->reussi()
        ->mobileMoney(OperateurMobileMoney::MixxTMoney)
        ->create([
            'periode_annee' => 2026,
            'periode_mois' => 5,
        ]);

    $response = $this->actingAs($locataireUser)->post(route('locataire.paiements.store'), [
        'periode' => '2026-05',
        'montant' => '125000',
        'mode' => ModePaiement::MobileMoney->value,
        'operateur_mobile_money' => OperateurMobileMoney::Moov->value,
    ]);

    expect(Paiement::query()->count())->toBe(2)
        ->and(Paiement::query()->latest('id')->first()->operateur_mobile_money)->toBe(OperateurMobileMoney::Moov);

    $response->assertSessionHas('warning');
});

test('locataire without active contrat sees an empty paiement state', function () {
    $proprietaire = User::factory()->proprietaire()->create();
    $locataireUser = User::factory()->locataire()->create(['must_change_password' => false]);

    Locataire::factory()->for($locataireUser)->for($proprietaire, 'creePar')->create();

    $response = $this->actingAs($locataireUser)->get(route('locataire.paiements.index'));

    $response->assertSuccessful();
    $response->assertSee('Aucun contrat actif');
});
