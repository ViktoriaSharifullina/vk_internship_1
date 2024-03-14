<?php

namespace App\Repositories\Contracts;

use App\Models\Quest;

interface QuestRepositoryInterface
{
    public function find($id): Quest;
    public function create(array $data): Quest;
}
