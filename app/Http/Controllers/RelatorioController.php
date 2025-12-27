<?php

namespace App\Http\Controllers;

use App\Models\Corrida;
use App\Models\Apostador;
use App\Services\ControlePorCorridaService;
use Illuminate\Http\Request;

class RelatorioController extends Controller
{
    protected $controleService;

    public function __construct(ControlePorCorridaService $controleService)
    {
        $this->controleService = $controleService;
    }

    /**
     * Imprime o controle de apostas de um apostador específico
     *
     * @param int $corridaId
     * @param int $apostadorId
     * @return \Illuminate\View\View
     */
    public function imprimirControleApostador(int $corridaId, int $apostadorId)
    {
        $corrida = Corrida::findOrFail($corridaId);
        $apostador = Apostador::findOrFail($apostadorId);

        // Processar dados do apostador
        $dados = $this->controleService->processarApostador(
            $corrida,
            $apostadorId
        );

        if (!$dados) {
            abort(404, 'Apostador não encontrado nesta corrida');
        }

        // Retornar view com auto-impressão via JavaScript
        return view('relatorios.controle_apostador', [
            'corrida' => $corrida,
            'dados' => $dados,
            'autoPrint' => true,
        ]);
    }

    /**
     * Imprime o controle de todos os apostadores de uma corrida
     * Cada apostador inicia em uma nova página
     *
     * @param int $corridaId
     * @return \Illuminate\View\View
     */
    public function imprimirControleTodosApostadores(int $corridaId)
    {
        $corrida = Corrida::findOrFail($corridaId);

        // Processar todos os apostadores
        $todosDados = $this->controleService->processarCorrida($corrida);

        // Retornar view com todos os dados e auto-impressão
        return view('relatorios.controle_todos_apostadores', [
            'corrida' => $corrida,
            'todosDados' => $todosDados,
            'autoPrint' => true,
        ]);
    }
}

