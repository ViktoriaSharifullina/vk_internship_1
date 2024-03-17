<?php

namespace Tests\Unit;

use Mockery;
use App\Models\User;
use App\Services\UserService;
use App\Models\CompletedQuest;
use PHPUnit\Framework\TestCase;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Repositories\Contracts\CompletedQuestRepositoryInterface;

class UserServiceTest extends TestCase
{
    private $userRepositoryMock;
    private $completedQuestRepositoryMock;
    private $userService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepositoryMock = Mockery::mock(UserRepositoryInterface::class);
        $this->completedQuestRepositoryMock = Mockery::mock(CompletedQuestRepositoryInterface::class);
        $this->userService = new UserService($this->userRepositoryMock, $this->completedQuestRepositoryMock);
    }

    protected function setUpUserMock($id, $name, $balance = 0, $method = 'findOrFail')
    {
        $userMock = new User(['name' => $name, 'balance' => $balance]);
        $userMock->id = $id;

        $this->userRepositoryMock->shouldReceive($method)
            ->with($id)
            ->andReturn($userMock);
    }

    protected function setUpCompletedQuestsMock($userId, $questsData)
    {
        $completedQuests = collect($questsData)->map(function ($data) use ($userId) {
            return new CompletedQuest(['quest_id' => $data['quest_id'], 'user_id' => $userId]);
        });

        $this->completedQuestRepositoryMock->shouldReceive('getCompletedQuestsByUser')
            ->with($userId)
            ->andReturn($completedQuests);
    }

    public function testCreateUser()
    {
        $this->userRepositoryMock->shouldReceive('create')
            ->once()
            ->andReturn(new User([
                'name' => 'Test User',
                'balance' => 1000
            ]));

        $user = $this->userService->createUser(['name' => 'Test User', 'balance' => 1000]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('Test User', $user->name);
        $this->assertEquals(1000, $user->balance);
    }

    public function testFindUserById()
    {
        $this->setUpUserMock(1, "John Doe", 100);

        $result = $this->userService->findUserById(1);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals(1, $result->id);
        $this->assertEquals('John Doe', $result->name);
        $this->assertEquals(100, $result->balance);
    }

    public function testGetUserCompletedQuestsAndBalance()
    {
        $userId = 1;
        $this->setUpUserMock($userId, "John Doe", 1000);
        $this->setUpCompletedQuestsMock($userId, [
            ['quest_id' => 1],
            ['quest_id' => 2],
        ]);

        $result = $this->userService->getUserCompletedQuestsAndBalance($userId);

        $this->assertNotNull($result);
        $this->assertEquals(1000, $result['balance']);
        $this->assertCount(2, $result['completedQuests']);
    }

    public function testGetUserCompletedQuestsAndBalanceUserNotFound()
    {
        $userId = 1;
        $this->userRepositoryMock->shouldReceive('findOrFail')
            ->once()
            ->with($userId)
            ->andThrow(new ModelNotFoundException);

        $result = $this->userService->getUserCompletedQuestsAndBalance($userId);

        $this->assertNull($result);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}
