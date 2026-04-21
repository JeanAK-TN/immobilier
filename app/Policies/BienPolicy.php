<?php

namespace App\Policies;

use App\Models\Bien;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BienPolicy
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
    public function view(User $user, Bien $bien): bool
    {
        return $user->isProprietaire() && $bien->user_id === $user->id;
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
    public function update(User $user, Bien $bien): bool
    {
        return $user->isProprietaire() && $bien->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Bien $bien): Response
    {
        if (! $user->isProprietaire() || $bien->user_id !== $user->id) {
            return Response::deny('Vous ne pouvez pas supprimer ce bien.');
        }

        if ($bien->contrats()->exists()) {
            return Response::deny('Impossible de supprimer un bien lié à un ou plusieurs contrats.');
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Bien $bien): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Bien $bien): bool
    {
        return false;
    }
}
