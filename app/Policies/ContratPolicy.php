<?php

namespace App\Policies;

use App\Models\Contrat;
use App\Models\User;

class ContratPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isProprietaire();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Contrat $contrat): bool
    {
        if ($user->isProprietaire()) {
            return $contrat->bien->user_id === $user->id;
        }

        return $user->isLocataire() && $contrat->locataire->user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isProprietaire();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Contrat $contrat): bool
    {
        return $user->isProprietaire() && $contrat->bien->user_id === $user->id;
    }

    /**
     * Determine whether the locataire can sign the model.
     */
    public function sign(User $user, Contrat $contrat): bool
    {
        return $user->isLocataire()
            && $contrat->locataire->user_id === $user->id
            && $contrat->peutEtreSigne();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Contrat $contrat): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Contrat $contrat): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Contrat $contrat): bool
    {
        return false;
    }
}
