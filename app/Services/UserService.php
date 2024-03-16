<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\CompletedQuestRepositoryInterface;

class UserService
{
    protected $userRepository;
    protected $completedQuestRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        CompletedQuestRepositoryInterface $completedQuestRepository
    ) {
        $this->userRepository = $userRepository;
        $this->completedQuestRepository = $completedQuestRepository;
    }

    public function createUser(array $data): User
    {
        return $this->userRepository->create($data);
    }

    public function findUserById($id)
    {
        return $this->userRepository->find($id);
    }

    public function getUserCompletedQuestsAndBalance($userId)
    {
        $user = $this->userRepository->find($userId);

        if (!$user) {
            return null;
        }

        $completedQuests = $this->completedQuestRepository->getCompletedQuestsByUser($userId);
        $balance = $user->balance;

        return [
            'completedQuests' => $completedQuests,
            'balance' => $balance,
        ];
    }
}
