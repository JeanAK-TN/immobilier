<?php

namespace Database\Factories;

use App\Enums\CategorieTicket;
use App\Enums\PrioriteTicket;
use App\Enums\StatutTicket;
use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Locataire;
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
        $proprietaire = User::factory()->proprietaire();
        $locataireUser = User::factory()->locataire();
        $locataire = Locataire::factory()
            ->for($locataireUser)
            ->for($proprietaire, 'creePar');
        $contrat = Contrat::factory()
            ->for(Bien::factory()->for($proprietaire, 'proprietaire'))
            ->for($locataire)
            ->actif();

        return [
            'contrat_id' => $contrat,
            'soumis_par_user_id' => $locataireUser,
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
