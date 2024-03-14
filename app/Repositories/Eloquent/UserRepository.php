<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    public function find($id): ?User
    {
        return User::find($id);
    }

    public function updateBalance(User $user, $amount): bool
    {
        $user->balance += $amount;
        return $user->save();
    }

    public function create(array $data): User
    {
        return User::create($data);
    }
}
