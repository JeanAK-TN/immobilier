<?php

namespace Database\Factories;

use App\Models\Contrat;
use App\Models\Paiement;
use App\Models\Quittance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Quittance>
 */
class QuittanceFactory extends Factory
{
    public function definition(): array
    {
        $date = fake()->dateTimeBetween('-12 months', 'now');
        $contrat = Contrat::factory()->actif();

        return [
            'contrat_id' => $contrat,
            'paiement_id' => Paiement::factory()->reussi()->for($contrat),
            'generee_par_user_id' => User::factory()->proprietaire(),
            'periode_mois' => (int) $date->format('n'),
            'periode_annee' => (int) $date->format('Y'),
            'numero_quittance' => sprintf('QUIT-%s-%04d', $date->format('Y'), fake()->numberBetween(1, 9999)),
            'emise_le' => Carbon::instance($date),
            'fichier_path' => null,
        ];
    }
}
