<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConversacionesController;
use App\Http\Controllers\ComentariosController;
use App\Http\Controllers\NbaController;
use App\Http\Controllers\GameCommentController;

// Rutas públicas
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::get('games/{id}/comments', [GameCommentController::class, 'index']);
Route::get('/nba/schedule/{date}', [NbaController::class, 'getSchedule']);
Route::get('/nba/calendario', [NbaController::class, 'calendario']);
Route::get('/nba/game/{id}', [NbaController::class, 'getGameDetail']);

// Rutas protegidas con JWT
Route::middleware('auth:api')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);

    // Conversaciones
    Route::get('conversaciones', [ConversacionesController::class, 'index']);
    Route::get('conversaciones/{id}', [ConversacionesController::class, 'show']);
    Route::post('conversaciones', [ConversacionesController::class, 'store']);
    Route::put('conversaciones/{id}', [ConversacionesController::class, 'update']);
    Route::delete('conversaciones/{id}', [ConversacionesController::class, 'destroy']);

    // Comentarios
    Route::post('conversaciones/{conversacion_id}/comentarios', [ComentariosController::class, 'store']);
    Route::put('comentarios/{id}', [ComentariosController::class, 'update']);
    Route::delete('comentarios/{id}', [ComentariosController::class, 'destroy']);
    Route::get('conversaciones/{conversacion_id}/comentarios', [ComentariosController::class, 'index']);

    // Comentarios en partidos NBA
    Route::post('/games/{id}/comments', [GameCommentController::class, 'store']);
    Route::put('/games/comments/{commentId}', [GameCommentController::class, 'update']);
    Route::delete('/games/comments/{commentId}', [GameCommentController::class, 'destroy']);
});
