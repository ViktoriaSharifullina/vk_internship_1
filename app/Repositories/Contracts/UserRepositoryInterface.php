<?php

namespace App\Repositories\Contracts;

use App\Models\User;

interface UserRepositoryInterface
{
    public function findOrFail($id): ?User;
    public function updateBalance(User $user, $amount): bool;
    public function create(array $data): User;
}
