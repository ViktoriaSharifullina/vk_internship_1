<?php

namespace App\Services;

use App\Models\User;
use App\Models\Quest;
use App\Models\CompletedQuest;
use Illuminate\Support\Facades\Log;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\QuestRepositoryInterface;
use App\Repositories\Contracts\CompletedQuestRepositoryInterface;

class QuestService
{
    protected $userRepository;
    protected $questRepository;
    protected $completedQuestRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        QuestRepositoryInterface $questRepository,
        CompletedQuestRepositoryInterface $completedQuestRepository
    ) {
        $this->userRepository = $userRepository;
        $this->questRepository = $questRepository;
        $this->completedQuestRepository = $completedQuestRepository;
    }

    public function createQuest(array $data)
    {
        return $this->questRepository->create($data);
    }

    public function completeQuest($userId, $questId)
    {
        Log::info("completeQuest");
        $user = $this->userRepository->find($userId);
        if (!$user) {
            return ['success' => false, 'message' => 'User not found'];
        }

        $quest = $this->questRepository->find($questId);
        if (!$quest) {
            return ['success' => false, 'message' => 'Quest not found'];
        }


        if ($this->completedQuestRepository->isQuestCompletedByUser($userId, $questId)) {
            return ['success' => false, 'message' => "This quest has already been completed by the user."];
        }

        $this->completedQuestRepository->create([
            'user_id' => $userId,
            'quest_id' => $questId
        ]);

        $this->userRepository->updateBalance($user, $quest->cost);

        return ['success' => true];
    }

    public function getAllQuests()
    {
        return $this->questRepository->all();
    }
}
