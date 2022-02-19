<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ConversationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('login', [AuthController::class, 'signin']);
Route::post('register', [AuthController::class, 'signup']);

//chat logs routes
Route::get('/logs/{conversationId}', [ChatController::class, 'getChatLogsByConversationId']);
Route::post('/logs', [ChatController::class, 'store']);
Route::post('/logs/{conversationId}', [ChatController::class, 'getLatestChatLogsByConversationId']);

//conversations routes
Route::get('/conversations/{userId}', [ConversationController::class, 'findConversationsByUserId']);
Route::post('/conversations', [ConversationController::class, 'createConversations']);
Route::post('/conversations/read', [ConversationController::class, 'markAsRead']);
Route::delete('/conversations/{conversationId}', [ConversationController::class, 'removeEmptyConversation']);

//users routes
Route::get('/users/{userId}', [UserController::class, 'getAllExceptCurrent']);