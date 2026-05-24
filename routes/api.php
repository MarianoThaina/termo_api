<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;

Route::options('/{any}', function () {
    return response()->json([], 200);
})->where('any', '.*');

Route::post('/iniciar-jogo', [GameController::class, 'startGame']);

Route::post('/validar-tentativa', [GameController::class, 'validateGuess']);