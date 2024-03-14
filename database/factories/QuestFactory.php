<?php

namespace Database\Factories;

use App\Models\Quest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Quest>
 */
class QuestFactory extends Factory
{
    protected $model = Quest::class;

    public function definition()
    {
        return [
            'name' => $this->faker->sentence,
            'cost' => $this->faker->numberBetween(50, 500),
        ];
    }
}
