<?php

namespace App\Repositories\Eloquent;

use App\Models\CompletedQuest;
use Illuminate\Support\Collection;
use App\Exceptions\QuestAlreadyCompletedException;
use App\Repositories\Contracts\CompletedQuestRepositoryInterface;

class CompletedQuestRepository implements CompletedQuestRepositoryInterface
{
    public function create(array $attributes): CompletedQuest
    {
        return CompletedQuest::create($attributes);
    }

    public function isQuestCompletedByUser($userId, $questId)
    {
        $isCompleted = CompletedQuest::where('user_id', $userId)
            ->where('quest_id', $questId)
            ->exists();

        if ($isCompleted) {
            throw new QuestAlreadyCompletedException();
        }
    }

    public function getCompletedQuestsByUser($userId): Collection
    {
        $completedQuests = CompletedQuest::with('quest')
            ->where('user_id', $userId)
            ->get();

        return $completedQuests;
    }
}
