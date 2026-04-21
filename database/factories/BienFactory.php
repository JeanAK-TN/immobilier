<?php

namespace Database\Factories;

use App\Enums\StatutBien;
use App\Enums\TypeBien;
use App\Models\Bien;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Bien>
 */
class BienFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->proprietaire(),
            'nom' => fake()->words(3, true),
            'type' => fake()->randomElement(TypeBien::cases()),
            'adresse' => fake()->streetAddress(),
            'ville' => fake()->city(),
            'pays' => fake()->country(),
            'description' => fake()->optional()->paragraph(),
            'statut' => StatutBien::Disponible,
        ];
    }

    public function occupe(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => StatutBien::Occupe,
        ]);
    }

    public function disponible(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => StatutBien::Disponible,
        ]);
    }
}
