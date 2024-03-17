<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UserService;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'balance' => 'required|numeric',
        ]);

        $user = $this->userService->createUser($validatedData);

        return response()->json($user, Response::HTTP_CREATED);
    }

    public function show($id)
    {
        $user = $this->userService->findUserById($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($user);
    }

    public function getUserCompletedQuestsAndBalance($userId)
    {

        $data = $this->userService->getUserCompletedQuestsAndBalance($userId);

        if ($data === null) {
            return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($data);
    }
}
