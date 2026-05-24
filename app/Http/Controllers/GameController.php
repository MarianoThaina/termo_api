<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GameController extends Controller
{
    // Busca todas as palavras do arquivo palavras.txt
    private function getWords(): array
    {
        $path = storage_path('words/palavras.txt');

        // Retorna as palavras em formato de array
        return file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    }

    // Endpoint responsável por iniciar uma nova partida
    public function startGame()
    {
        // Busca lista de palavras
        $words = $this->getWords();

        // Escolhe uma palavra aleatória
        $word = $words[array_rand($words)];

        // Cria o jogo no banco
        $game = Game::create([
            'game_id' => Str::uuid(),
            'secret_word' => $word,
            'attempts' => 0,
            'won' => false
        ]);

        // Retorna informações iniciais da partida
        return response()->json([
            'idJogo' => $game->game_id,
            'tamanhoPalavra' => 5,
            'tentativasMaximas' => 6
        ], 200);
    }

    // Endpoint responsável por validar tentativas
    public function validateGuess(Request $request)
    {
        // Validação dos dados recebidos
        $validator = validator($request->all(), [
            'idJogo' => 'required',
            'palavra' => 'required|string|size:5'
        ]);

        // Retorna erro caso os dados estejam inválidos
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Requisição inválida',
                'errors' => $validator->errors()
            ], 400);
        }

        // Procura o jogo no banco usando o id
        $game = Game::where('game_id', $request->idJogo)->first();

        // Retorna erro caso o jogo não exista
        if (!$game) {
            return response()->json([
                'message' => 'Jogo não encontrado'
            ], 404);
        }

        // Impede novas tentativas após 6 jogadas
        if ($game->attempts >= 6) {
            return response()->json([
                'message' => 'Limite de tentativas atingido'
            ], 400);
        }

        // Palavra enviada pelo jogador
        $guess = strtolower($request->palavra);

        // Verifica se a palavra existe no dicionário
        if (!in_array($guess, $this->getWords())) {
            return response()->json([
                'resultado' => [],
                'venceu' => false,
                'tentativasRestantes' => 6 - $game->attempts,
                'palavraValida' => false
            ], 200);
        }

        // Palavra secreta do jogo
        $secret = strtolower($game->secret_word);

        // Array que armazenará os resultados
        $result = [];

        // Percorre cada letra da palavra
        for ($i = 0; $i < 5; $i++) {

            // Letra correta e posição correta
            if ($guess[$i] === $secret[$i]) {

                $status = 'correta';

            // Letra existe na palavra mas em posição diferente
            } elseif (str_contains($secret, $guess[$i])) {

                $status = 'presente';

            // Letra não existe na palavra
            } else {

                $status = 'ausente';
            }

            // Adiciona resultado da letra ao array
            $result[] = [
                'letra' => $guess[$i],
                'status' => $status
            ];
        }

        // Soma uma tentativa ao jogo
        $game->attempts += 1;

        // Verifica vitória
        $won = $guess === $secret;

        // Marca vitória no banco
        if ($won) {
            $game->won = true;
        }

        // Salva alterações
        $game->save();

        // Retorna resultado da jogada
        return response()->json([
            'resultado' => $result,
            'venceu' => $won,
            'tentativasRestantes' => 6 - $game->attempts,
            'palavraValida' => true
        ], 200);
    }
}
