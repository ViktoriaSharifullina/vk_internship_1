<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
        try {
            $user = $this->userRepository->findOrFail($id);
            return $user;
        } catch (ModelNotFoundException $e) {
            return null;
        }
    }

    public function getUserCompletedQuestsAndBalance($userId)
    {
        try {
            $user = $this->userRepository->findOrFail($userId);

            $completedQuests = $this->completedQuestRepository->getCompletedQuestsByUser($userId);
            $balance = $user->balance;

            return [
                'completedQuests' => $completedQuests,
                'balance' => $balance,
            ];
        } catch (ModelNotFoundException $e) {
            return null;
        }
    }
}
