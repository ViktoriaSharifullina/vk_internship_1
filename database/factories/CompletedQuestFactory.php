<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Quest;
use App\Models\CompletedQuest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CompletedQuest>
 */
class CompletedQuestFactory extends Factory
{
    protected $model = CompletedQuest::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'quest_id' => Quest::factory(),
        ];
    }
}
