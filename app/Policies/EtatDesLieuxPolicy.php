<?php

namespace App\Policies;

use App\Models\EtatDesLieux;
use App\Models\User;

class EtatDesLieuxPolicy
{
    public function viewAny(): bool
    {
        return true;
    }

    public function view(User $user, EtatDesLieux $etatDesLieux): bool
    {
        return $user->id === $etatDesLieux->user_id;
    }

    public function create(): bool
    {
        return true;
    }

    public function update(User $user, EtatDesLieux $etatDesLieux): bool
    {
        return $user->id === $etatDesLieux->user_id;
    }

    public function delete(User $user, EtatDesLieux $etatDesLieux): bool
    {
        return $user->id === $etatDesLieux->user_id;
    }
}