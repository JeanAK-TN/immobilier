<?php

namespace App\Policies;

use App\Models\Paiement;
use App\Models\Quittance;
use App\Models\User;

class QuittancePolicy
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
    public function view(User $user, Quittance $quittance): bool
    {
        if ($user->isProprietaire()) {
            return $quittance->contrat->bien->user_id === $user->id;
        }

        return $user->isLocataire() && $quittance->contrat->locataire->user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Paiement $paiement): bool
    {
        return $user->isProprietaire()
            && $paiement->contrat->bien->user_id === $user->id
            && $paiement->isReussi();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Quittance $quittance): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Quittance $quittance): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Quittance $quittance): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Quittance $quittance): bool
    {
        return false;
    }
}
