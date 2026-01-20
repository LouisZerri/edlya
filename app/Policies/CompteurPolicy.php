<?php

namespace App\Policies;

use App\Models\Compteur;
use App\Models\User;

class CompteurPolicy
{
    public function viewAny(): bool
    {
        return true;
    }

    public function view(User $user, Compteur $compteur): bool
    {
        return $user->id === $compteur->etatDesLieux->user_id;
    }

    public function create(): bool
    {
        return true;
    }

    public function update(User $user, Compteur $compteur): bool
    {
        return $user->id === $compteur->etatDesLieux->user_id;
    }

    public function delete(User $user, Compteur $compteur): bool
    {
        return $user->id === $compteur->etatDesLieux->user_id;
    }
}
