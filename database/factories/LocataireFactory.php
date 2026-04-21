<?php

namespace Database\Factories;

use App\Models\Locataire;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Locataire>
 */
class LocataireFactory extends Factory
{
    public function definition(): array
    {
        $prenom = fake()->firstName();
        $nom = fake()->lastName();

        return [
            'user_id' => User::factory()->locataire(),
            'cree_par_user_id' => User::factory()->proprietaire(),
            'prenom' => $prenom,
            'nom' => $nom,
            'telephone' => fake()->optional()->phoneNumber(),
            'email' => fake()->unique()->safeEmail(),
            'piece_identite_path' => null,
        ];
    }
}
