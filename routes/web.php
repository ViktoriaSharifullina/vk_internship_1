<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\QuestController;


// Маршруты для пользователей
Route::post('/users', [UserController::class, 'store']);
Route::get('/users/{id}', [UserController::class, 'show']);
Route::get('/users/{userId}/completed-quests', [UserController::class, 'getUserCompletedQuestsAndBalance']);


// Маршруты для заданий
Route::post('/quests', [QuestController::class, 'store']);
Route::get('/quests', [QuestController::class, 'index']);

// Маршрут для выполнения задания
Route::post('/quests/complete', [QuestController::class, 'complete']);
