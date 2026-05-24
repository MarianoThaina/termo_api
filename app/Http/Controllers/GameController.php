<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GameController extends Controller
{
    private function getWords(): array
    {
        $path = storage_path('words/palavras.txt');

        return file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    }

    public function startGame()
    {
        $words = $this->getWords();

        $word = $words[array_rand($words)];

        $game = Game::create([
            'game_id' => Str::uuid(),
            'secret_word' => $word,
            'attempts' => 0,
            'won' => false
        ]);

        return response()->json([
            'idJogo' => $game->game_id,
            'tamanhoPalavra' => 5,
            'tentativasMaximas' => 6
        ], 200);
    }

    public function validateGuess(Request $request)
    {
        $request->validate([
            'idJogo' => 'required',
            'palavra' => 'required|string|size:5'
        ]);

        $game = Game::where('game_id', $request->idJogo)->first();

        if (!$game) {
            return response()->json([
                'message' => 'Jogo não encontrado'
            ], 404);
        }

        $guess = strtolower($request->palavra);

        $secret = strtolower($game->secret_word);

        $result = [];

        for ($i = 0; $i < 5; $i++) {

            if ($guess[$i] === $secret[$i]) {

                $status = 'correta';

            } elseif (str_contains($secret, $guess[$i])) {

                $status = 'presente';

            } else {

                $status = 'ausente';
            }

            $result[] = [
                'letra' => $guess[$i],
                'status' => $status
            ];
        }

        $game->attempts += 1;

        $won = $guess === $secret;

        if ($won) {
            $game->won = true;
        }

        $game->save();

        return response()->json([
            'resultado' => $result,
            'venceu' => $won,
            'tentativasRestantes' => 6 - $game->attempts,
            'palavraValida' => in_array($guess, $this->getWords())
        ], 200);
    }
}
