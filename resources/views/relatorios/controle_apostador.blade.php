<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Controle de Apostas - {{ $dados['apostador']['nome'] }}</title>

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

        /* ===== HEADER ===== */
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #000;
            page-break-inside: avoid;
        }

        .header-logo {
            width: 90px;
            text-align: center;
        }

        .header-logo img {
            max-width: 100%;
            height: auto;
        }

        .header-center {
            flex: 1;
            text-align: center;
        }

        .header-center h1 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .header-center h2 {
            font-size: 14px;
            font-weight: normal;
            color: #333;
        }

        /* ===== INFO ===== */
        .info-section {
            margin-bottom: 20px;
        }

        .info-row {
            display: flex;
            gap: 5px;
            margin-bottom: 8px;
            padding: 5px 0;
            border-bottom: 1px solid #ddd;
        }

        .info-label {
            font-weight: bold;
        }

        .info-value {
            text-align: right;
        }

        /* ===== TABLE ===== */
        .apostas-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
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

        /* ===== TOTAIS ===== */
        .flex {
            display: flex;
            gap: 15px;
        }

        .apostador-totals {
            flex: 1;
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
        }

        .apostador-totals-value {
            font-weight: bold;
        }

        .text-right {
            text-align: right;
        }

        .text-bold {
            font-weight: bold;
        }
    </style>
</head>

<body>
<div class="container page-break">

    <!-- HEADER -->
    <div class="header">
        <div class="header-logo">
            <img src="{{ asset('images/logo-esquerda.png') }}" alt="Logo Esquerda">
        </div>

        <div class="header-center">
            <h1>CONTROLE DE APOSTAS</h1>
            <h2>{{ $corrida->nome }}</h2>
            <h2>Data: {{ $corrida->data->format('d/m/Y H:i') }}</h2>
        </div>

        <div class="header-logo">
            <!-- <img src="{{ public_path('images/logo-direita.png') }}" alt="Logo Direita"> -->
        </div>
    </div>

    <!-- INFO -->
    <div class="info-section">
        <div class="info-row">
            <span class="info-label">Apostador:</span>
            <span class="info-value">{{ $dados['apostador']['nome'] }}</span>
        </div>
    </div>

    <!-- TABELA -->
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
        @foreach($dados['rodadas'] as $rodada)
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
        @endforeach
        </tbody>
    </table>

    <!-- TOTAIS -->
    <div class="flex">
        <div class="apostador-totals">
            <h3>PRÊMIOS POR ANIMAL</h3>
            @foreach($dados['premios_por_animal'] as $animal => $premio)
                <div class="apostador-totals-row">
                    <span class="apostador-totals-label">Animal {{ $animal }}:</span>
                    <span class="apostador-totals-value">R$ {{ number_format($premio, 2, ',', '.') }}</span>
                </div>
            @endforeach
        </div>

        <div class="apostador-totals">
            <h3>TOTAIS DO APOSTADOR</h3>
            <div class="apostador-totals-row">
                <span class="apostador-totals-label">Total Jogo:</span>
                <span class="apostador-totals-value">R$ {{ number_format($dados['totais']['total_valor_apostador'], 2, ',', '.') }}</span>
            </div>
            <div class="apostador-totals-row">
                <span class="apostador-totals-label">Total Lo:</span>
                <span class="apostador-totals-value">R$ {{ number_format($dados['totais']['total_lo_apostador'], 2, ',', '.') }}</span>
            </div>
            <div class="apostador-totals-row">
                <span class="apostador-totals-label">Total Geral:</span>
                <span class="apostador-totals-value">R$ {{ number_format($dados['totais']['total_geral_apostador'], 2, ',', '.') }}</span>
            </div>
        </div>
    </div>
</div>

@if(isset($autoPrint) && $autoPrint)
<script>
    window.onload = function () {
        window.print();
    };
</script>
@endif

</body>
</html>
