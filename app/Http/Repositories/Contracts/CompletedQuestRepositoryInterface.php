<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use App\Models\Quest;
use App\Models\CompletedQuest;
use Illuminate\Support\Collection;


interface CompletedQuestRepositoryInterface
{
    public function create(array $attributes): CompletedQuest;
    public function isQuestCompletedByUser($userId, $questId): bool;
    public function getCompletedQuestsByUser($userId): Collection;
}
