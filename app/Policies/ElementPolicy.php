<?php

namespace App\Policies;

use App\Models\Element;
use App\Models\User;

class ElementPolicy
{
    public function viewAny(): bool
    {
        return true;
    }

    public function view(User $user, Element $element): bool
    {
        return $user->id === $element->piece->etatDesLieux->user_id;
    }

    public function create(): bool
    {
        return true;
    }

    public function update(User $user, Element $element): bool
    {
        return $user->id === $element->piece->etatDesLieux->user_id;
    }

    public function delete(User $user, Element $element): bool
    {
        return $user->id === $element->piece->etatDesLieux->user_id;
    }
}
