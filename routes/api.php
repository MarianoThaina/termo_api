<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;

// Rota OPTIONS usada para evitar problemas de CORS
Route::options('/{any}', function () {
    return response()->json([], 200);
})->where('any', '.*');

// Endpoint para iniciar uma nova partida
Route::post('/iniciar-jogo', [GameController::class, 'startGame']);

// Endpoint para validar tentativa do jogador
Route::post('/validar-tentativa', [GameController::class, 'validateGuess']);
