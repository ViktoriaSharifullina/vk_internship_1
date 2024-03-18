<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Quest;

class QuestControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @testdox Test creation of a quest with valid data.
     */
    public function testStoreValidQuest()
    {
        $questData = [
            'name' => 'New Quest',
            'cost' => 50,
            'difficulty' => 'hard'
        ];

        $response = $this->json('POST', '/quests', $questData);

        $response->assertStatus(201)
            ->assertJson($questData);
    }

    /**
     * @testdox Test quest creation with invalid data.
     */
    public function testStoreInvalidQuest()
    {
        $invalidQuestData = [
            'cost' => 'not-a-number',
            'difficulty' => 'invalid'
        ];

        $response = $this->json('POST', '/quests', $invalidQuestData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'cost', 'difficulty']);
    }

    /**
     * @testdox Test quest completion by a user.
     */
    public function testCompleteQuest()
    {
        $user = User::factory()->create();
        $quest = Quest::factory()->create();

        $data = [
            'user_id' => $user->id,
            'quest_id' => $quest->id,
        ];

        $response = $this->json('POST', '/quests/complete', $data);

        $response->assertStatus(201)
            ->assertJson(['message' => 'Quest completed successfully']);
    }

    /**
     * @testdox Test quest completion with non-existent user.
     */
    public function testCompleteQuestWithNonExistentUser()
    {
        $data = [
            'user_id' => 9999,
            'quest_id' => 1,
        ];

        $response = $this->json('POST', '/quests/complete', $data);

        $response->assertStatus(404);
    }

    /**
     * @testdox Test quest completion with non-existent quest.
     */
    public function testCompleteQuestWithNonExistentQuest()
    {
        $user = User::factory()->create();
        $data = [
            'user_id' => $user->id,
            'quest_id' => 9999,
        ];

        $response = $this->json('POST', '/quests/complete', $data);

        $response->assertStatus(404);
    }

    /**
     * @testdox Test quest completion by a user when the quest is already completed.
     */
    public function testCompleteQuestAlreadyCompleted()
    {
        $user = User::factory()->create();
        $quest = Quest::factory()->create();

        $initialCompletionData = [
            'user_id' => $user->id,
            'quest_id' => $quest->id,
        ];
        $this->json('POST', '/quests/complete', $initialCompletionData);

        $repeatCompletionData = [
            'user_id' => $user->id,
            'quest_id' => $quest->id,
        ];
        $response = $this->json('POST', '/quests/complete', $repeatCompletionData);

        $response->assertStatus(400)
            ->assertJson(['message' => 'This quest has already been completed by the user']);
    }

    /**
     * @testdox Test retrieving all quests.
     */
    public function testIndex()
    {
        $quests = Quest::factory()->count(3)->create();

        $response = $this->json('GET', '/quests');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }
}
