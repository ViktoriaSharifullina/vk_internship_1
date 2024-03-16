<?php

namespace Tests\Unit;

use Mockery;
use App\Models\User;
use App\Services\UserService;
use App\Models\CompletedQuest;
use PHPUnit\Framework\TestCase;
use App\Repositories\Contracts\UserRepositoryInterface;
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
        $userMock = new User(['name' => 'John Doe', 'balance' => 1000]);
        $userMock->id = 1;

        $this->userRepositoryMock->shouldReceive('find')
            ->once()
            ->with(1)
            ->andReturn($userMock);

        $result = $this->userService->findUserById(1);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals(1, $result->id);
        $this->assertEquals('John Doe', $result->name);
        $this->assertEquals(1000, $result->balance);
    }

    public function testGetUserCompletedQuestsAndBalanceUserFound()
    {
        $userId = 1;
        $userMock = Mockery::mock(User::class);
        $userMock->shouldReceive('getAttribute')->with('balance')->andReturn(1000);

        $this->userRepositoryMock->shouldReceive('find')
            ->once()
            ->with($userId)
            ->andReturn($userMock);

        $completedQuestsMock = collect([
            new CompletedQuest(['quest_id' => 1, 'user_id' => $userId]),
            new CompletedQuest(['quest_id' => 2, 'user_id' => $userId])
        ]);

        $this->completedQuestRepositoryMock->shouldReceive('getCompletedQuestsByUser')
            ->once()
            ->with($userId)
            ->andReturn($completedQuestsMock);



        $result = $this->userService->getUserCompletedQuestsAndBalance($userId);

        $this->assertNotNull($result);
        $this->assertEquals(1000, $result['balance']);
        $this->assertCount(2, $result['completedQuests']);
    }

    public function testGetUserCompletedQuestsAndBalanceUserNotFound()
    {
        $userId = 1;
        $this->userRepositoryMock->shouldReceive('find')
            ->once()
            ->with($userId)
            ->andReturn(null);

        $result = $this->userService->getUserCompletedQuestsAndBalance($userId);

        $this->assertNull($result);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}
