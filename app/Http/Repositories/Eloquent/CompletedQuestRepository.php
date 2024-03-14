<?php

namespace App\Repositories\Eloquent;

use App\Models\CompletedQuest;
use App\Repositories\Contracts\CompletedQuestRepositoryInterface;
use Illuminate\Support\Collection;

class CompletedQuestRepository implements CompletedQuestRepositoryInterface
{
    public function create(array $attributes): CompletedQuest
    {
        return CompletedQuest::create($attributes);
    }

    public function isQuestCompletedByUser($userId, $questId): bool
    {
        return CompletedQuest::where('user_id', $userId)
            ->where('quest_id', $questId)
            ->exists();
    }

    public function getCompletedQuestsByUser($userId): Collection
    {
        return CompletedQuest::where('user_id', $userId)->get();
    }
}
