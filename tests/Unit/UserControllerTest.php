<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @testdox Creative a new user with valid data
     */
    public function testCreateUser()
    {
        $userData = [
            'name' => 'New User',
            'balance' => 0,
        ];

        $response = $this->json('POST', '/users', $userData);

        $response->assertStatus(201)
            ->assertJson([
                'name' => $userData['name'],
                'balance' => $userData['balance'],
            ]);
    }

    /**
     * @testdox Creative a new user with invalid data
     */
    public function testCreateUserWithInvalidData()
    {
        $invalidUserData = [
            'name' => '',
            'balance' => 'not-a-number',
        ];

        $response = $this->json('POST', '/users', $invalidUserData);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'name',
                'balance',
            ],
        ]);
    }

    /**
     * @testdox Get user data
     */
    public function testShowDataOfUser()
    {
        $user = User::factory()->create();

        $response = $this->json('GET', "/users/{$user->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $user->id,
                'name' => $user->name,
                'balance' => $user->balance,
            ]);
    }

    /**
     * @testdox Get user data about a non-existent user
     */
    public function testShowDataOfNonExistentUser()
    {
        $response = $this->json('GET', "/users/999999");

        $response->assertStatus(404)
            ->assertJson(['message' => 'Entity not found']);
    }

    /**
     * @testdox Get user completed test and balance
     */
    public function testGetUserCompletedQuests()
    {
        $user = User::factory()->create();

        $response = $this->getJson("/users/{$user->id}/completed-quests");

        $response->assertStatus(200)
            ->assertJson([
                'completedQuests' => [],
                'balance' => $user->balance,
            ]);
    }

    /**
     * @testdox Get user completed test and balance when user not found
     */
    public function testGetUserCompletedQuestsUserNotFound()
    {
        $response = $this->getJson("/users/999/completed-quests");

        $response->assertStatus(404)
            ->assertJson(['message' => 'Entity not found']);
    }
}
