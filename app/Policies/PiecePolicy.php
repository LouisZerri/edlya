<?php

namespace App\Policies;

use App\Models\Piece;
use App\Models\User;

class PiecePolicy
{
    public function viewAny(): bool
    {
        return true;
    }

    public function view(User $user, Piece $piece): bool
    {
        return $user->id === $piece->etatDesLieux->user_id;
    }

    public function create(): bool
    {
        return true;
    }

    public function update(User $user, Piece $piece): bool
    {
        return $user->id === $piece->etatDesLieux->user_id;
    }

    public function delete(User $user, Piece $piece): bool
    {
        return $user->id === $piece->etatDesLieux->user_id;
    }
}
