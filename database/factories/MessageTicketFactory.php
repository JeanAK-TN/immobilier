<?php

namespace Database\Factories;

use App\Models\MessageTicket;
use App\Models\TicketMaintenance;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MessageTicket>
 */
class MessageTicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ticket_maintenance_id' => TicketMaintenance::factory(),
            'user_id' => User::factory(),
            'message' => fake()->paragraph(),
            'est_note_interne' => false,
        ];
    }
}
