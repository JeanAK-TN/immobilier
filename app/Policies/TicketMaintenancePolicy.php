<?php

namespace App\Policies;

use App\Models\TicketMaintenance;
use App\Models\User;

class TicketMaintenancePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isProprietaire() || $user->isLocataire();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TicketMaintenance $ticketMaintenance): bool
    {
        if ($user->isProprietaire()) {
            return $ticketMaintenance->contrat->bien->user_id === $user->id;
        }

        return $user->isLocataire() && $ticketMaintenance->contrat->locataire->user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isLocataire() && $user->locataire?->contratActif() !== null;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TicketMaintenance $ticketMaintenance): bool
    {
        return $user->isProprietaire() && $ticketMaintenance->contrat->bien->user_id === $user->id;
    }

    public function reply(User $user, TicketMaintenance $ticketMaintenance): bool
    {
        return $this->view($user, $ticketMaintenance) && $ticketMaintenance->peutRecevoirMessage();
    }

    public function changeStatus(User $user, TicketMaintenance $ticketMaintenance): bool
    {
        return $this->update($user, $ticketMaintenance);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TicketMaintenance $ticketMaintenance): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TicketMaintenance $ticketMaintenance): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TicketMaintenance $ticketMaintenance): bool
    {
        return false;
    }
}
