<?php

namespace Database\Factories;

use App\Enums\CategorieTicket;
use App\Enums\PrioriteTicket;
use App\Enums\StatutTicket;
use App\Models\Contrat;
use App\Models\TicketMaintenance;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TicketMaintenance>
 */
class TicketMaintenanceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'contrat_id' => Contrat::factory()->actif(),
            'soumis_par_user_id' => User::factory()->locataire(),
            'titre' => fake()->sentence(5),
            'categorie' => fake()->randomElement(CategorieTicket::cases()),
            'priorite' => fake()->randomElement(PrioriteTicket::cases()),
            'description' => fake()->paragraph(),
            'statut' => StatutTicket::Ouvert,
        ];
    }

    public function enCours(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => StatutTicket::EnCours,
        ]);
    }

    public function resolu(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => StatutTicket::Resolu,
        ]);
    }
}
