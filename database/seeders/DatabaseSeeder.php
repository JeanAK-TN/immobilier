<?php

namespace Database\Seeders;

use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Locataire;
use App\Models\Paiement;
use App\Models\TicketMaintenance;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $proprietaire = User::factory()->proprietaire()->create([
            'name' => 'Jean Dupont',
            'email' => 'proprietaire@exemple.fr',
            'password' => Hash::make('password'),
        ]);

        $userLocataire1 = User::factory()->locataire()->create([
            'name' => 'Marie Martin',
            'email' => 'locataire1@exemple.fr',
            'password' => Hash::make('password'),
            'must_change_password' => false,
        ]);

        $userLocataire2 = User::factory()->locataire()->create([
            'name' => 'Pierre Durand',
            'email' => 'locataire2@exemple.fr',
            'password' => Hash::make('password'),
            'must_change_password' => true,
        ]);

        $locataire1 = Locataire::factory()->create([
            'user_id' => $userLocataire1->id,
            'cree_par_user_id' => $proprietaire->id,
            'prenom' => 'Marie',
            'nom' => 'Martin',
            'email' => $userLocataire1->email,
            'telephone' => '06 12 34 56 78',
        ]);

        $locataire2 = Locataire::factory()->create([
            'user_id' => $userLocataire2->id,
            'cree_par_user_id' => $proprietaire->id,
            'prenom' => 'Pierre',
            'nom' => 'Durand',
            'email' => $userLocataire2->email,
            'telephone' => '07 98 76 54 32',
        ]);

        $bien1 = Bien::factory()->occupe()->create([
            'user_id' => $proprietaire->id,
            'nom' => 'Appartement Bellecour',
            'type' => 'appartement',
            'adresse' => '12 Place Bellecour',
            'ville' => 'Lyon',
            'pays' => 'France',
            'description' => 'Beau T3 en plein centre-ville, lumineux et calme.',
        ]);

        $bien2 = Bien::factory()->disponible()->create([
            'user_id' => $proprietaire->id,
            'nom' => 'Studio République',
            'type' => 'appartement',
            'adresse' => '5 Rue de la République',
            'ville' => 'Lyon',
            'pays' => 'France',
            'description' => 'Studio moderne en plein centre, idéal pour étudiant ou jeune actif.',
        ]);

        $contrat1 = Contrat::factory()->actif()->create([
            'bien_id' => $bien1->id,
            'locataire_id' => $locataire1->id,
            'date_debut' => now()->subYear(),
            'loyer_mensuel' => 850,
            'depot_garantie' => 1700,
            'charges' => 80,
            'jour_paiement' => 5,
        ]);

        $paiement = Paiement::factory()->reussi()->create([
            'contrat_id' => $contrat1->id,
            'periode_mois' => now()->month,
            'periode_annee' => now()->year,
            'montant' => 930,
            'mode' => 'virement',
        ]);

        TicketMaintenance::factory()->create([
            'contrat_id' => $contrat1->id,
            'soumis_par_user_id' => $userLocataire1->id,
            'titre' => 'Fuite sous l\'évier de la cuisine',
            'categorie' => 'plomberie',
            'priorite' => 'haute',
            'description' => 'Il y a une fuite d\'eau sous l\'évier depuis hier matin. L\'eau coule lentement mais en continu.',
        ]);
    }
}
