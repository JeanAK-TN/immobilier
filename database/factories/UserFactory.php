<?php

namespace Database\Factories;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => Role::Locataire,
            'must_change_password' => false,
            'is_active' => true,
            'remember_token' => Str::random(10),
        ];
    }

    public function proprietaire(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => Role::Proprietaire,
            'must_change_password' => false,
        ]);
    }

    public function locataire(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => Role::Locataire,
            'must_change_password' => true,
        ]);
    }

    public function inactif(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
