<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\UserService;
use App\Repositories\Contracts\UserRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Mockery;

class UserServiceTest extends TestCase
{
    public function testCreateUser()
    {
        $repository = Mockery::mock(UserRepositoryInterface::class);

        $repository->shouldReceive('create')
            ->once()
            ->andReturn(new User([
                'name' => 'Test User',
                'balance' => 1000
            ]));

        $service = new UserService($repository);

        $user = $service->createUser(['name' => 'Test User', 'balance' => 1000]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('Test User', $user->name);
        $this->assertEquals(1000, $user->balance);
    }

    public function testFindUserById()
    {
        $userRepositoryMock = Mockery::mock(UserRepositoryInterface::class);
        $userMock = new User(['name' => 'John Doe', 'balance' => 1000]);
        $userMock->id = 1;

        $userRepositoryMock->shouldReceive('find')
            ->once()
            ->with(1)
            ->andReturn($userMock);

        $userService = new UserService($userRepositoryMock);

        $result = $userService->findUserById(1);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals(1, $result->id);
        $this->assertEquals('John Doe', $result->name);
        $this->assertEquals(1000, $result->balance);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}
