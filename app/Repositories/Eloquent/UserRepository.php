<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Exceptions\NotFoundException;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserRepository implements UserRepositoryInterface
{
    public function findOrFail($id): ?User
    {
        $entity = User::find($id);
        if (!$entity) {
            throw (new ModelNotFoundException())->setModel(User::class, $id);
        }
        return $entity;
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
