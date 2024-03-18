<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\QuestService;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use App\Exceptions\QuestAlreadyCompletedException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class QuestController extends Controller
{
    protected $questService;

    public function __construct(QuestService $questService)
    {
        $this->questService = $questService;
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'cost' => 'required|numeric',
            'difficulty' => 'required|string|in:easy,hard,normal,expert'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();
        $quest = $this->questService->createQuest($validatedData);

        return response()->json($quest, Response::HTTP_CREATED);
    }

    public function complete(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required',
            'quest_id' => 'required',
        ]);

        try {
            $result = $this->questService->completeQuest($validated['user_id'], $validated['quest_id']);

            return response()->json(['message' => 'Quest completed successfully'], Response::HTTP_CREATED);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Entity not found'], 404);
        } catch (QuestAlreadyCompletedException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function index()
    {
        $quests = $this->questService->getAllQuests();

        return response()->json($quests);
    }
}
