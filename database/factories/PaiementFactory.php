<?php

namespace Database\Factories;

use App\Enums\ModePaiement;
use App\Enums\StatutPaiement;
use App\Models\Contrat;
use App\Models\Paiement;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Paiement>
 */
class PaiementFactory extends Factory
{
    public function definition(): array
    {
        $date = fake()->dateTimeBetween('-12 months', 'now');

        return [
            'contrat_id' => Contrat::factory()->actif(),
            'periode_mois' => (int) $date->format('n'),
            'periode_annee' => (int) $date->format('Y'),
            'montant' => fake()->numberBetween(400, 2200),
            'mode' => fake()->randomElement(ModePaiement::cases()),
            'reference' => 'PAY-'.strtoupper(Str::random(10)),
            'statut' => StatutPaiement::SimuleReussi,
            'notes' => null,
        ];
    }

    public function reussi(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => StatutPaiement::SimuleReussi,
        ]);
    }

    public function echoue(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => StatutPaiement::SimuleEchec,
        ]);
    }
}
