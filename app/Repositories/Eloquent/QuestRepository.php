<?php

namespace App\Repositories\Eloquent;

use App\Models\Quest;
use App\Exceptions\NotFoundException;
use Illuminate\Support\Collection;
use App\Repositories\Contracts\QuestRepositoryInterface;

class QuestRepository implements QuestRepositoryInterface
{
    public function findOrFail($id): ?Quest
    {
        $entity = Quest::find($id);
        if (!$entity) {
            throw new NotFoundException();
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
