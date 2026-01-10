<?php

namespace App\Policies;

use App\Models\Logement;
use App\Models\User;

class LogementPolicy
{
    public function viewAny(): bool
    {
        return true;
    }

    public function view(User $user, Logement $logement): bool
    {
        return $user->id === $logement->user_id;
    }

    public function create(): bool
    {
        return true;
    }

    public function update(User $user, Logement $logement): bool
    {
        return $user->id === $logement->user_id;
    }

    public function delete(User $user, Logement $logement): bool
    {
        return $user->id === $logement->user_id;
    }
}