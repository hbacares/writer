<?php

namespace App\Policies;

use App\Models\Story;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class StoryPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Story $story): bool
    {
        return $user->id === $story->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Story $story): bool
    {
        return $user->id === $story->user_id;
    }

    public function delete(User $user, Story $story): bool
    {
        return $user->id === $story->user_id;
    }

    public function restore(User $user, Story $story): bool
    {
        return $user->id === $story->user_id;
    }

    public function forceDelete(User $user, Story $story): bool
    {
        return $user->id === $story->user_id;
    }
}
