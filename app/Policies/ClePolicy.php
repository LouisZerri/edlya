<?php

namespace App\Policies;

use App\Models\Cle;
use App\Models\User;

class ClePolicy
{
    public function viewAny(): bool
    {
        return true;
    }

    public function view(User $user, Cle $cle): bool
    {
        return $user->id === $cle->etatDesLieux->user_id;
    }

    public function create(): bool
    {
        return true;
    }

    public function update(User $user, Cle $cle): bool
    {
        return $user->id === $cle->etatDesLieux->user_id;
    }

    public function delete(User $user, Cle $cle): bool
    {
        return $user->id === $cle->etatDesLieux->user_id;
    }
}
