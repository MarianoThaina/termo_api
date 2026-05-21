<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;

Route::post('/iniciar-jogo', [GameController::class, 'startGame']);

Route::post('/validar-tentativa', [GameController::class, 'validateGuess']);