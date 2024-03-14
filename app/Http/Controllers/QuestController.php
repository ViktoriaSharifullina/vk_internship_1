<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\QuestService;

class QuestController extends Controller
{
    protected $questService;

    public function __construct(QuestService $questService)
    {
        $this->questService = $questService;
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'cost' => 'required|numeric|min:0',
        ]);

        $quest = $this->questService->createQuest($validatedData);

        return response()->json($quest, 201);
    }

    public function complete(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'quest_id' => 'required|exists:quests,id',
        ]);

        $result = $this->questService->completeQuest($validated['user_id'], $validated['quest_id']);

        if ($result['success']) {
            return response()->json(['message' => 'Quest completed successfully'], 201);
        } else {
            return response()->json(['message' => $result['message']], 400);
        }
    }
}
