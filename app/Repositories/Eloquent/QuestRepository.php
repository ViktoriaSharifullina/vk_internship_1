<?php

namespace App\Repositories\Eloquent;

use App\Models\Quest;
use Illuminate\Support\Collection;
use App\Repositories\Contracts\QuestRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class QuestRepository implements QuestRepositoryInterface
{
    public function findOrFail($id): ?Quest
    {
        $entity = Quest::find($id);
        if (!$entity) {
            throw (new ModelNotFoundException())->setModel(Quest::class, $id);
        }
        return $entity;
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
