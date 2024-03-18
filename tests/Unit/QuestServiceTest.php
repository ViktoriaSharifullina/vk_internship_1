<?php

namespace Tests\Unit;

use Mockery;
use App\Models\User;
use App\Models\Quest;
use App\Models\CompletedQuest;
use App\Services\QuestService;
use PHPUnit\Framework\TestCase;
use App\Exceptions\NotFoundException;
use Illuminate\Database\Eloquent\Collection;
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

    private function setUpUserMock($userId = 1, $balance = 0)
    {
        $userMock = Mockery::mock(User::class);
        $userMock->shouldReceive('getAttribute')->with('balance')->andReturn($balance);
        $this->userRepositoryMock->shouldReceive('findOrFail')->with($userId)->andReturn($userMock);
        return $userMock;
    }

    private function setUpQuestMock($questId = 1, $cost = 100, $difficulty = 'normal')
    {
        $questMock = Mockery::mock(Quest::class)->makePartial();
        $questMock->cost = $cost;
        $questMock->difficulty = $difficulty;
        $this->questRepositoryMock->shouldReceive('findOrFail')->with($questId)->andReturn($questMock);
        return $questMock;
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

        $this->setUpUserMock($userId, 0);

        $this->setUpQuestMock($questId, $baseCost, $difficulty);

        $this->userRepositoryMock->shouldReceive('updateBalance')
            ->once()
            ->with(Mockery::type(User::class), $reward)
            ->andReturn(true);

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

        $this->setUpUserMock($userId);

        $this->questRepositoryMock->shouldReceive('findOrFail')
            ->with($questId)
            ->andThrow(new NotFoundException('Quest not found'));

        $result = $this->questService->completeQuest($userId, $questId);

        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
        $this->assertEquals('Quest not found', $result['message']);
    }

    public function testCompleteQuestUserNotFound()
    {
        $userId = 999;
        $questId = 1;

        $this->userRepositoryMock->shouldReceive('findOrFail')
            ->with($userId)
            ->andThrow(new NotFoundException('User not found'));

        $result = $this->questService->completeQuest($userId, $questId);

        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
        $this->assertEquals('User not found', $result['message']);
    }

    public function testCompleteQuestAlreadyCompleted()
    {
        $userId = 1;
        $questId = 1;

        $this->setUpUserMock($userId, 0);

        $this->setUpQuestMock($questId);

        $this->completedQuestRepositoryMock->shouldReceive('isQuestCompletedByUser')
            ->with($userId, $questId)
            ->andThrow(new QuestAlreadyCompletedException());

        $result = $this->questService->completeQuest($userId, $questId);

        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
        $this->assertEquals("This quest has already been completed by the user", $result['message']);
    }

    public function testGetAllQuestsReturnsCorrectQuests()
    {
        $questsCollection = collect([
            new Quest(['name' => 'Quest 1', 'cost' => 100, 'difficulty' => 'easy']),
            new Quest(['name' => 'Quest 2', 'cost' => 200, 'difficulty' => 'hard'])
        ]);

        $this->questRepositoryMock->shouldReceive('all')
            ->once()
            ->andReturn($questsCollection);

        $quests = $this->questService->getAllQuests();

        $this->assertCount(2, $quests);
        $this->assertEquals('Quest 1', $quests->first()->name);
        $this->assertEquals('Quest 2', $quests->last()->name);
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

    public function testGetAllQuestsReturnsEmptyArrayWhenNoQuestsAvailable()
    {
        $this->questRepositoryMock->shouldReceive('all')
            ->once()
            ->andReturn(Collection::make([]));

        $quests = $this->questService->getAllQuests();

        $this->assertInstanceOf(Collection::class, $quests);
        $this->assertTrue($quests->isEmpty());
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}
