<?php

namespace App\Services;

use App\Models\User;
use App\Models\Quest;
use App\Models\CompletedQuest;
use Illuminate\Support\Facades\Log;
use App\Exceptions\NotFoundException;
use App\Exceptions\QuestAlreadyCompletedException;
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
        try {
            $user = $this->userRepository->findOrFail($userId);
            $quest = $this->questRepository->findOrFail($questId);

            $this->completedQuestRepository->isQuestCompletedByUser($userId, $questId);
            $reward = $this->calculateReward($quest);

            $this->completedQuestRepository->create([
                'user_id' => $userId,
                'quest_id' => $questId
            ]);

            $this->userRepository->updateBalance($user, $reward);

            return ['success' => true];
        } catch (NotFoundException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        } catch (QuestAlreadyCompletedException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function getAllQuests()
    {
        return $this->questRepository->all();
    }

    public function calculateReward(Quest $quest)
    {
        $baseCost = $quest->cost;
        switch ($quest->difficulty) {
            case 'easy':
                return $baseCost;
            case 'normal':
                return $baseCost * 1.2;
            case 'hard':
                return $baseCost * 1.5;
            case 'expert':
                return $baseCost * 2;
            default:
                return $baseCost;
        }
    }
}
