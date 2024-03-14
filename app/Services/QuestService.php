<?php

namespace App\Services;

use App\Models\User;
use App\Models\Quest;
use App\Models\CompletedQuest;
use Illuminate\Support\Facades\DB;

class QuestService
{
    public function createQuest(array $data): Quest
    {
        return Quest::create($data);
    }

    public function completeQuest($userId, $questId)
    {
        DB::beginTransaction();

        try {
            $user = User::findOrFail($userId);
            $quest = Quest::findOrFail($questId);

            if ($this->questAlreadyCompleted($userId, $questId)) {
                throw new \Exception("This quest has already been completed by the user.");
            }

            $this->registerCompletedQuest($userId, $questId);
            $this->updateUserBalance($user, $quest->cost);

            DB::commit();

            return ['success' => true];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    protected function questAlreadyCompleted($userId, $questId)
    {
        return CompletedQuest::where('user_id', $userId)->where('quest_id', $questId)->exists();
    }

    protected function registerCompletedQuest($userId, $questId)
    {
        CompletedQuest::create([
            'user_id' => $userId,
            'quest_id' => $questId
        ]);
    }

    protected function updateUserBalance($user, $amount)
    {
        $user->balance += $amount;
        $user->save();
    }
}
