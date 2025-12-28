<?php

namespace App\Services;

use App\Models\Corrida;
use App\Models\Aposta;
use Illuminate\Support\Collection;

class ControlePorCorridaService
{
    /**
     * Processa todas as apostas de uma corrida e calcula os prêmios
     * com rateio proporcional quando múltiplos apostadores apostam
     * no mesmo animal vencedor.
     *
     * @param Corrida $corrida
     * @return array Estrutura organizada: Apostador → Rodadas → Apostas → Prêmio
     */
    public function processarCorrida(Corrida $corrida): array
    {
        // Buscar todas as apostas da corrida com relacionamentos
        $apostas = Aposta::where('corrida_id', $corrida->id)
            ->with(['apostador'])
            ->get();

        // Agrupar por apostador
        $apostasPorApostador = $apostas->groupBy('apostador_id');

        // Estrutura final de retorno
        $resultado = [];

        // Processar cada apostador
        foreach ($apostasPorApostador as $apostadorId => $apostasApostador) {
            $apostador = $apostasApostador->first()->apostador;

            $premiosPorAnimal = $this->calculaPremiosPorAnimal($corrida, $apostasApostador, $apostas);
            
            // Agrupar apostas por rodada
            $apostasPorRodada = $apostasApostador->groupBy('rodada');

            $rodadasProcessadas = [];
            $totalValorApostador = 0;
            $totalLoApostador = 0;
            $totalRecebidoApostador = 0;

            // Processar cada rodada do apostador
            foreach ($apostasPorRodada as $rodada => $apostasRodada) {
                $dadosRodada = $this->processarRodada(
                    $rodada,
                    $apostasRodada,
                    $corrida,
                    $apostas->where('rodada', $rodada)
                );

                $rodadasProcessadas[] = $dadosRodada;

                $totalValorApostador += $dadosRodada['total_valor_rodada'];
                $totalLoApostador += $dadosRodada['total_lo_rodada'];
            }

            $resultado[] = [
                'apostador' => [
                    'id' => $apostador->id,
                    'nome' => $apostador->nome,
                ],
                'rodadas' => $rodadasProcessadas,
                'premios_por_animal' => $premiosPorAnimal,
                'totais' => [
                    'total_valor_apostador' => $totalValorApostador,
                    'total_lo_apostador' => $totalLoApostador,
                    'total_geral_apostador' => $totalValorApostador + $totalLoApostador,
                ],
            ];
        }

        return $resultado;
    }

    public function calculaPremiosPorAnimal($corrida, $apostasApostador, $todasApostas)
    {
        $premios = [];
        

        foreach($apostasApostador as $aposta){

            $premioAposta = 0;

            $todasApostasRodada = $todasApostas->where('rodada', $aposta->rodada);

            $totalValorRodada = $todasApostasRodada->sum('valor');
            $totalLoRodada = $todasApostasRodada->sum('lo');

            // Aplicar taxa da corrida
            $taxa = $corrida->taxa / 100; // Converter percentual para decimal
            $premioLiquidoRodada = ($totalValorRodada + $totalLoRodada) * (1 - $taxa);

            // Calcular total apostado no animal vencedor (todos os apostadores)
            $totalApostadoAnimal = 
                $todasApostasRodada->where('animal', $aposta->animal)->sum('valor') + 
                $todasApostasRodada->where('animal', $aposta->animal)->sum('lo');

        

            // Rateio proporcional
            if ($totalApostadoAnimal > 0) {
                $proporcao = ($aposta->lo + $aposta->valor) / $totalApostadoAnimal;
                $premioAposta = $premioLiquidoRodada * $proporcao;
            }

            $premios[$aposta->animal] = 
                array_key_exists($aposta->animal, $premios) 
                ? $premios[$aposta->animal] + $premioAposta 
                : $premioAposta;
        }        

        return $premios;
    }

    /**
     * Processa uma rodada específica para um apostador
     *
     * @param int $rodada
     * @param Collection $apostasApostadorRodada Apostas do apostador nesta rodada
     * @param Corrida $corrida
     * @param Collection $todasApostasRodada Todas as apostas da rodada (todos apostadores)
     * @return array
     */
    private function processarRodada(
        int $rodada,
        Collection $apostasApostadorRodada,
        Corrida $corrida,
        Collection $todasApostasRodada
    ): array {
        // Calcular total apostado na rodada (todos os apostadores)
        $totalValorRodada = $todasApostasRodada->sum('valor');
        $totalLoRodada = $todasApostasRodada->sum('lo');

        // Aplicar taxa da corrida
        $taxa = $corrida->taxa / 100; // Converter percentual para decimal
        $premioLiquidoRodada = ($totalValorRodada + $totalLoRodada) * (1 - $taxa);

        $totalValorRodadaApostador = $apostasApostadorRodada->sum('valor');
        $totalLoRodadaApostador = $apostasApostadorRodada->sum('lo');
        // Total apostado pelo apostador nesta rodada
        $totalApostadoApostadorRodada = $totalValorRodadaApostador + $totalLoRodadaApostador;

        // Processar apostas do apostador
        $apostasProcessadas = [];
        $premiosPorAnimal = [];
        $premioRecebidoRodada = 0;

        foreach ($apostasApostadorRodada as $aposta) {
            $premioAposta = 0;

            // Calcular total apostado no animal vencedor (todos os apostadores)
            $totalApostadoAnimal = 
                $todasApostasRodada->where('animal', $aposta->animal)->sum('valor') + 
                $todasApostasRodada->where('animal', $aposta->animal)->sum('lo');

        

            // Rateio proporcional
            if ($totalApostadoAnimal > 0) {
                $proporcao = ($aposta->lo + $aposta->valor) / $totalApostadoAnimal;
                $premioAposta = $premioLiquidoRodada * $proporcao;
            }

            $apostasProcessadas[] = [
                'id' => $aposta->id,
                'animal' => $aposta->animal,
                'valor' => $aposta->valor ?? 0, // Usando 'valor' como 'jogo'
                'lo' => $aposta->lo,
                'premio' => $premioAposta
            ];
            
            $premioRecebidoRodada += $premioAposta;
        }

        return [
            'rodada' => $rodada,
            'apostas' => $apostasProcessadas,
            'total_valor_rodada' => $totalValorRodadaApostador,
            'total_lo_rodada' => $totalLoRodadaApostador,
            'total_apostado_rodada' => $totalValorRodada + $totalLoRodada,
            'taxa_aplicada' => $corrida->taxa,
            'premio_liquido_rodada' => $premioLiquidoRodada,
            'total_apostado_rodada_apostador' => $totalApostadoApostadorRodada,
            'premio_recebido' => $premioRecebidoRodada,
            'premios_por_animal' => $premiosPorAnimal
        ];
    }

    /**
     * Busca dados de um apostador específico em uma corrida
     *
     * @param Corrida $corrida
     * @param int $apostadorId
     * @param array $vencedoresPorRodada
     * @return array|null
     */
    public function processarApostador(Corrida $corrida, int $apostadorId): ?array
    {
        $resultado = $this->processarCorrida($corrida);

        foreach ($resultado as $dadosApostador) {
            if ($dadosApostador['apostador']['id'] == $apostadorId) {
                return $dadosApostador;
            }
        }

        return null;
    }
}

