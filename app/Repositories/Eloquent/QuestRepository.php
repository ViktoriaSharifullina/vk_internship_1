<?php

namespace App\Repositories\Eloquent;

use App\Models\Quest;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\Contracts\QuestRepositoryInterface;

class QuestRepository implements QuestRepositoryInterface
{
    public function find($id): ?Quest
    {
        return Quest::find($id);
    }

    public function create(array $data): Quest
    {
        return Quest::create($data);
    }

    public function all(): Collection
    {
        return Quest::all();
    }
}
