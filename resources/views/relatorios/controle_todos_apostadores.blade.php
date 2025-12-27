<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Controle de Apostas - Todos os Apostadores</title>
    <style>
        @page {
            size: A4;
            margin: 1.5cm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #000;
            background: #fff;
        }

        .page-break {
            page-break-after: always;
        }

        .container {
            width: 100%;
            max-width: 100%;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #000;
        }

        .header h1 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .header h2 {
            font-size: 14px;
            font-weight: normal;
            color: #333;
        }

        .info-section {
            margin-bottom: 20px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            padding: 5px 0;
            border-bottom: 1px solid #ddd;
        }

        .info-label {
            font-weight: bold;
            width: 40%;
        }

        .info-value {
            width: 60%;
            text-align: right;
        }

        .rodada-section {
            margin-bottom: 25px;
            border: 1px solid #000;
            padding: 10px;
            background: #f9f9f9;
        }

        .rodada-header {
            background: #333;
            color: #fff;
            padding: 8px;
            margin: -10px -10px 10px -10px;
            font-weight: bold;
            font-size: 13px;
        }

        .apostas-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .apostas-table th {
            background: #555;
            color: #fff;
            padding: 8px;
            text-align: left;
            font-size: 11px;
            border: 1px solid #000;
        }

        .apostas-table td {
            padding: 6px 8px;
            border: 1px solid #000;
            font-size: 11px;
        }

        .apostas-table tbody tr:nth-child(even) {
            background: #f0f0f0;
        }

        .apostas-table tbody tr:hover {
            background: #e0e0e0;
        }

        .vencedor {
            background: #90EE90 !important;
            font-weight: bold;
        }

        .rodada-totals {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 2px solid #000;
        }

        .rodada-totals-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 11px;
        }

        .rodada-totals-label {
            font-weight: bold;
        }

        .rodada-totals-value {
            text-align: right;
        }

        .apostador-totals {
            margin-top: 20px;
            padding: 15px;
            border: 2px solid #000;
            background: #fff;
        }

        .apostador-totals h3 {
            font-size: 14px;
            margin-bottom: 10px;
            text-align: center;
            background: #333;
            color: #fff;
            padding: 8px;
            margin: -15px -15px 15px -15px;
        }

        .apostador-totals-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            padding: 5px 0;
            border-bottom: 1px solid #ddd;
            font-size: 12px;
        }

        .apostador-totals-label {
            font-weight: bold;
            font-size: 13px;
        }

        .apostador-totals-value {
            font-weight: bold;
            font-size: 13px;
            text-align: right;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .text-bold {
            font-weight: bold;
        }
    </style>
</head>
<body>
    @foreach($todosDados as $dados)
        <div class="container page-break">
            <div class="header">
                <h1>CONTROLE DE APOSTAS</h1>
                <h2>{{ $corrida->nome }}</h2>
                <h2>Data: {{ $corrida->data->format('d/m/Y H:i') }}</h2>
            </div>

            <div class="info-section">
                <div class="info-row">
                    <span class="info-label">Apostador:</span>
                    <span class="info-value">{{ $dados['apostador']['nome'] }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Taxa da Corrida:</span>
                    <span class="info-value">{{ number_format($corrida->taxa, 2, ',', '.') }}%</span>
                </div>
            </div>

            @foreach($dados['rodadas'] as $rodada)
                <div class="rodada-section">
                    <div class="rodada-header">
                        RODADA {{ $rodada['rodada'] }}
                    </div>

                    <table class="apostas-table">
                        <thead>
                            <tr>
                                <th>RODADA</th>
                                <th>ANIMAL</th>
                                <th>JOGO</th>
                                <th>L.O</th>
                                <th>PRÊMIO</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rodada['apostas'] as $aposta)
                                <tr>
                                    <td>{{ $rodada['rodada'] }}</td>
                                    <td>{{ $aposta['animal'] }}</td>
                                    <td class="text-right">R$ {{ number_format($aposta['valor'], 2, ',', '.') }}</td>
                                    <td class="text-right">R$ {{ number_format($aposta['lo'], 2, ',', '.') }}</td>
                                    <td class="text-right">
                                        @if($aposta['premio'] > 0)
                                            <span class="text-bold">R$ {{ number_format($aposta['premio'], 2, ',', '.') }}</span>
                                        @else
                                            R$ 0,00
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="rodada-totals">
                        <div class="rodada-totals-row">
                            <span class="rodada-totals-label">Total Apostado na Rodada (todos):</span>
                            <span class="rodada-totals-value">R$ {{ number_format($rodada['total_apostado_rodada'], 2, ',', '.') }}</span>
                        </div>
                        <div class="rodada-totals-row">
                            <span class="rodada-totals-label">Taxa Aplicada:</span>
                            <span class="rodada-totals-value">{{ number_format($rodada['taxa_aplicada'], 2, ',', '.') }}%</span>
                        </div>
                        <div class="rodada-totals-row">
                            <span class="rodada-totals-label">Prêmio Líquido da Rodada:</span>
                            <span class="rodada-totals-value text-bold">R$ {{ number_format($rodada['premio_liquido_rodada'], 2, ',', '.') }}</span>
                        </div>
                        <div class="rodada-totals-row">
                            <span class="rodada-totals-label">Total Apostado pelo Apostador:</span>
                            <span class="rodada-totals-value">R$ {{ number_format($rodada['total_apostado_rodada_apostador'], 2, ',', '.') }}</span>
                        </div>
                        <div class="rodada-totals-row">
                            <span class="rodada-totals-label">Prêmio Recebido:</span>
                            <span class="rodada-totals-value text-bold">R$ {{ number_format($rodada['premio_recebido'], 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="apostador-totals">
                <h3>TOTAIS DO APOSTADOR</h3>
                <div class="apostador-totals-row">
                    <span class="apostador-totals-label">Total Apostado:</span>
                    <span class="apostador-totals-value">R$ {{ number_format($dados['totais']['total_apostado'], 2, ',', '.') }}</span>
                </div>
                <div class="apostador-totals-row">
                    <span class="apostador-totals-label">Total Recebido:</span>
                    <span class="apostador-totals-value">R$ {{ number_format($dados['totais']['total_recebido'], 2, ',', '.') }}</span>
                </div>
                <div class="apostador-totals-row" style="border-top: 2px solid #000; margin-top: 10px; padding-top: 10px;">
                    <span class="apostador-totals-label">Saldo Final:</span>
                    <span class="apostador-totals-value" style="font-size: 14px;">
                        @if(($dados['totais']['total_recebido'] - $dados['totais']['total_apostado']) >= 0)
                         + R$
                        @else
                         - R$
                        @endif
                         {{ number_format($dados['totais']['total_recebido'] - $dados['totais']['total_apostado'], 2, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>
    @endforeach

    @if(isset($autoPrint) && $autoPrint)
        <script>
            // Auto-impressão quando a página carregar
            window.onload = function() {
                window.print();
            };

            // Fechar janela após impressão (opcional, funciona no NativePHP)
            window.onafterprint = function() {
                // Não fechar automaticamente para permitir visualização
                // window.close();
            };
        </script>
    @endif
</body>
</html>

