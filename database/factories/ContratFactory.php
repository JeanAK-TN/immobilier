<?php

namespace Database\Factories;

use App\Enums\StatutContrat;
use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Locataire;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Contrat>
 */
class ContratFactory extends Factory
{
    public function definition(): array
    {
        $dateDebut = fake()->dateTimeBetween('-2 years', 'now');

        return [
            'bien_id' => Bien::factory(),
            'locataire_id' => Locataire::factory(),
            'date_debut' => $dateDebut,
            'date_fin' => null,
            'reconduction_auto' => fake()->boolean(),
            'loyer_mensuel' => fake()->numberBetween(400, 2000),
            'depot_garantie' => fake()->numberBetween(400, 4000),
            'charges' => fake()->numberBetween(0, 200),
            'jour_paiement' => fake()->numberBetween(1, 28),
            'statut' => StatutContrat::Brouillon,
            'document_path' => null,
            'signe_le' => null,
            'signe_nom' => null,
            'signe_ip' => null,
        ];
    }

    public function actif(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => StatutContrat::Actif,
            'signe_le' => now(),
            'signe_nom' => fake()->name(),
            'signe_ip' => fake()->ipv4(),
        ]);
    }

    public function enAttenteSignature(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => StatutContrat::EnAttente,
        ]);
    }

    public function resilie(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => StatutContrat::Resilie,
            'date_fin' => fake()->dateTimeBetween('-6 months', 'now'),
        ]);
    }
}
