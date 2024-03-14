<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;

class UserService
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function createUser(array $data): User
    {
        return $this->userRepository->create($data);
    }

    public function findUserById($id)
    {
        return $this->userRepository->find($id);
    }
}
