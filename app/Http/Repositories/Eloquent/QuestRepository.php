<?php

namespace App\Repositories\Eloquent;

use App\Models\Quest;
use App\Repositories\Contracts\QuestRepositoryInterface;

class QuestRepository implements QuestRepositoryInterface
{
    public function find($id): ?Quest
    {
        return Quest::find($id);
    }
}
