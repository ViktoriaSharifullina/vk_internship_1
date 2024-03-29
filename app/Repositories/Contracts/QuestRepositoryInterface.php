<?php

namespace App\Repositories\Contracts;

use App\Models\Quest;
use Illuminate\Support\Collection;

interface QuestRepositoryInterface
{
    public function findOrFail($id): ?Quest;
    public function create(array $data): Quest;
    public function all(): Collection;
}
