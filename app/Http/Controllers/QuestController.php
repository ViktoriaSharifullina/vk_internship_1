<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\QuestService;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

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
            'difficulty' => 'required|string|in:easy,medium,hard,expert',
        ]);

        $quest = $this->questService->createQuest($validatedData);

        return response()->json($quest, Response::HTTP_CREATED);
    }

    public function complete(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required',
            'quest_id' => 'required',
        ]);

        $result = $this->questService->completeQuest($validated['user_id'], $validated['quest_id']);


        if (!$result['success']) {
            return response()->json(['message' => $result['message']], 400);
        }


        return response()->json(['message' => 'Quest completed successfully'], Response::HTTP_CREATED);
    }

    public function index()
    {
        $quests = $this->questService->getAllQuests();

        return response()->json($quests);
    }
}
