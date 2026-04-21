<?php

use App\Enums\OperateurMobileMoney;
use App\Enums\StatutPaiement;
use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Locataire;
use App\Models\Paiement;
use App\Models\User;

test('proprietaire can list and filter only their paiements', function () {
    $proprietaire = User::factory()->proprietaire()->create();
    $autreProprietaire = User::factory()->proprietaire()->create();

    $bien = Bien::factory()->for($proprietaire, 'proprietaire')->create(['nom' => 'Villa Hedzranawoe']);
    $locataire = Locataire::factory()->for($proprietaire, 'creePar')->create(['prenom' => 'Kossi', 'nom' => 'Mensah']);
    $contrat = Contrat::factory()->for($bien)->for($locataire)->actif()->create();

    $paiementVisible = Paiement::factory()
        ->for($contrat)
        ->mobileMoney(OperateurMobileMoney::Moov)
        ->create([
            'periode_annee' => 2026,
            'periode_mois' => 5,
            'statut' => StatutPaiement::SimuleReussi,
        ]);

    $paiementMasque = Paiement::factory()
        ->for(Contrat::factory()
            ->for(Bien::factory()->for($autreProprietaire, 'proprietaire'))
            ->for(Locataire::factory()->for($autreProprietaire, 'creePar'))
            ->actif())
        ->create();

    $response = $this->actingAs($proprietaire)->get(route('proprietaire.paiements.index', [
        'bien_id' => $bien->id,
        'locataire_id' => $locataire->id,
        'periode' => '2026-05',
        'statut' => StatutPaiement::SimuleReussi->value,
    ]));

    $response->assertSuccessful();
    $response->assertSee($paiementVisible->reference);
    $response->assertSee('Moov');
    $response->assertDontSee($paiementMasque->reference);
});

test('proprietaire can view a paiement receipt', function () {
    $proprietaire = User::factory()->proprietaire()->create();
    $paiement = Paiement::factory()
        ->for(Contrat::factory()
            ->for(Bien::factory()->for($proprietaire, 'proprietaire'))
            ->for(Locataire::factory()->for($proprietaire, 'creePar'))
            ->actif())
        ->mobileMoney(OperateurMobileMoney::MixxTMoney)
        ->create();

    $response = $this->actingAs($proprietaire)->get(route('proprietaire.paiements.show', $paiement));

    $response->assertSuccessful();
    $response->assertSee($paiement->reference);
    $response->assertSee('Paiement simulé - aucune transaction réelle');
});

test('proprietaire cannot view another proprietaires paiement', function () {
    $proprietaire = User::factory()->proprietaire()->create();
    $autreProprietaire = User::factory()->proprietaire()->create();

    $paiement = Paiement::factory()
        ->for(Contrat::factory()
            ->for(Bien::factory()->for($autreProprietaire, 'proprietaire'))
            ->for(Locataire::factory()->for($autreProprietaire, 'creePar'))
            ->actif())
        ->create();

    $response = $this->actingAs($proprietaire)->get(route('proprietaire.paiements.show', $paiement));

    $response->assertForbidden();
});
