<?php

namespace Tests\Unit;

use Mockery;
use App\Models\User;
use App\Models\Quest;
use App\Models\CompletedQuest;
use App\Services\QuestService;
use PHPUnit\Framework\TestCase;
use App\Exceptions\NotFoundException;
use App\Exceptions\QuestAlreadyCompletedException;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\QuestRepositoryInterface;
use App\Repositories\Contracts\CompletedQuestRepositoryInterface;

class QuestServiceTest extends TestCase
{
    private $userRepositoryMock;
    private $questRepositoryMock;
    private $completedQuestRepositoryMock;
    private $questService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepositoryMock = Mockery::mock(UserRepositoryInterface::class);
        $this->questRepositoryMock = Mockery::mock(QuestRepositoryInterface::class);
        $this->completedQuestRepositoryMock = Mockery::mock(CompletedQuestRepositoryInterface::class);

        $this->questService = new QuestService($this->userRepositoryMock, $this->questRepositoryMock, $this->completedQuestRepositoryMock);
    }

    public function testCreateQuest()
    {
        $this->questRepositoryMock->shouldReceive('create')
            ->once()
            ->andReturn(new Quest([
                'name' => 'Test Quest',
                'cost' => 100,
                'difficulty' => 'normal'
            ]));

        $quest = $this->questService->createQuest([
            'name' => 'Test Quest',
            'cost' => 100,
            'difficulty' => 'normal'
        ]);

        $this->assertInstanceOf(Quest::class, $quest);
        $this->assertEquals('Test Quest', $quest->name);
        $this->assertEquals(100, $quest->cost);
        $this->assertEquals('normal', $quest->difficulty);
    }

    public function testCompleteQuestSuccess()
    {
        $userId = 1;
        $questId = 1;
        $baseCost = 100;
        $difficulty = 'normal';
        $reward = 120;

        $userMock = Mockery::mock(User::class);
        $userMock->shouldReceive('getAttribute')->with('balance')->andReturn(0);

        $questMock = Mockery::mock(Quest::class)->makePartial();
        $questMock->cost = $baseCost;
        $questMock->difficulty = $difficulty;

        $this->userRepositoryMock->shouldReceive('findOrFail')
            ->once()
            ->with($userId)
            ->andReturn($userMock);

        $this->userRepositoryMock->shouldReceive('updateBalance')
            ->once()
            ->with(Mockery::type(User::class), $reward)
            ->andReturn(true);

        $this->questRepositoryMock->shouldReceive('findOrFail')
            ->once()
            ->with($questId)
            ->andReturn($questMock);

        $this->completedQuestRepositoryMock->shouldReceive('isQuestCompletedByUser')
            ->once()
            ->with($userId, $questId)
            ->andReturn(false);

        $this->completedQuestRepositoryMock->shouldReceive('create')
            ->once()
            ->with([
                'user_id' => $userId,
                'quest_id' => $questId
            ])
            ->andReturn(new CompletedQuest);


        $result = $this->questService->completeQuest($userId, $questId);

        $this->assertIsArray($result);
        $this->assertTrue($result['success']);
    }

    public function testCompleteQuestQuestNotFound()
    {
        $userId = 1;
        $questId = 999;

        $this->userRepositoryMock->shouldReceive('findOrFail')->with($userId)->andReturn(Mockery::mock(User::class));

        $this->questRepositoryMock->shouldReceive('findOrFail')->with($questId)->andThrow(new NotFoundException('Quest not found'));

        $result = $this->questService->completeQuest($userId, $questId);

        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
        $this->assertEquals('Quest not found', $result['message']);
    }

    public function testCompleteQuestUserNotFound()
    {
        $userId = 999;
        $questId = 1;

        $this->userRepositoryMock->shouldReceive('findOrFail')->with($userId)->andThrow(new NotFoundException('User not found'));

        $result = $this->questService->completeQuest($userId, $questId);

        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
        $this->assertEquals('User not found', $result['message']);
    }

    public function testCompleteQuestAlreadyCompleted()
    {
        $userId = 1;
        $questId = 1;

        $userMock = Mockery::mock(User::class);
        $questMock = Mockery::mock(Quest::class);

        $this->userRepositoryMock->shouldReceive('findOrFail')->with($userId)->andReturn($userMock);
        $this->questRepositoryMock->shouldReceive('findOrFail')->with($questId)->andReturn($questMock);

        $this->completedQuestRepositoryMock->shouldReceive('isQuestCompletedByUser')
            ->with($userId, $questId)
            ->andThrow(new QuestAlreadyCompletedException());

        $result = $this->questService->completeQuest($userId, $questId);

        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
        $this->assertEquals("This quest has already been completed by the user.", $result['message']);
    }

    public function testRewardCalculation()
    {
        $easyQuest = new Quest(['cost' => 100, 'difficulty' => 'easy']);
        $this->assertEquals(100, $this->questService->calculateReward($easyQuest));

        $normalQuest = new Quest(['cost' => 100, 'difficulty' => 'normal']);
        $this->assertEquals(120,  $this->questService->calculateReward($normalQuest));

        $hardQuest = new Quest(['cost' => 100, 'difficulty' => 'hard']);
        $this->assertEquals(150,  $this->questService->calculateReward($hardQuest));

        $expertQuest = new Quest(['cost' => 100, 'difficulty' => 'expert']);
        $this->assertEquals(200,  $this->questService->calculateReward($expertQuest));

        $expertQuest = new Quest(['cost' => 100, 'difficulty' => 'none']);
        $this->assertEquals(100,  $this->questService->calculateReward($expertQuest));
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}
