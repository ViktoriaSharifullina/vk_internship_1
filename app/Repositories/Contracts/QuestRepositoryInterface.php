<?php

namespace App\Repositories\Contracts;

use App\Models\Quest;
use Illuminate\Database\Eloquent\Collection;

interface QuestRepositoryInterface
{
    public function find($id): ?Quest;
    public function create(array $data): Quest;
    public function all(): Collection;
}
