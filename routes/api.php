<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ForosController;
use App\Http\Controllers\ComentariosController;
use App\Http\Controllers\ConversacionesController;
use App\Http\Controllers\NbaController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);
});


Route::middleware('auth:api')->group(function () {

    Route::get('conversaciones', [ConversacionesController::class, 'index']);
    Route::get('conversaciones/{id}', [ConversacionesController::class, 'show']);
    Route::post('conversaciones', [ConversacionesController::class, 'store']);
    Route::put('conversaciones/{id}', [ConversacionesController::class, 'update']);
    Route::delete('conversaciones/{id}', [ConversacionesController::class, 'destroy']);

    Route::post('conversaciones/{conversacion_id}/comentarios', [ComentariosController::class, 'store']);
    Route::put('comentarios/{id}', [ComentariosController::class, 'update']);
    Route::delete('comentarios/{id}', [ComentariosController::class, 'destroy']);
    Route::get('conversaciones/{conversacion_id}/comentarios', [ComentariosController::class, 'index']);
});


Route::get('/nba/schedule/{date}', [NbaController::class, 'getSchedule']);
Route::get('/nba/calendario', [NbaController::class, 'calendario']);
Route::get('/nba/game/{id}', [NbaController::class, 'getGameDetail']);