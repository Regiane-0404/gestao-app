<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Proposta #{{ $proposta->id }}</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 12px;
            color: #333;
        }

        .container {
            width: 100%;
            margin: 0 auto;
        }

        .header,
        .footer {
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .details {
            margin-top: 30px;
            margin-bottom: 30px;
        }

        .details table {
            width: 100%;
            border-collapse: collapse;
        }

        .details th,
        .details td {
            padding: 8px;
            text-align: left;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .items-table th,
        .items-table td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        .items-table th {
            background-color: #f2f2f2;
        }

        .totals {
            float: right;
            width: 40%;
            margin-top: 20px;
        }

        .totals table {
            width: 100%;
        }

        .totals td {
            padding: 5px;
        }

        .text-right {
            text-align: right;
        }

        .font-bold {
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>PROPOSTA</h1>
            <p><strong>Número:</strong> #{{ $proposta->id }} | <strong>Data:</strong>
                {{ $proposta->data_proposta ? $proposta->data_proposta->format('d/m/Y') : 'N/A' }}</p>
        </div>

        <div class="details">
            <table>
                <tr>
                    <td style="width: 50%;">
                        <strong>CLIENTE:</strong><br>
                        {{ $proposta->cliente->nome }}<br>
                        NIF: {{ $proposta->cliente->nif }}<br>
                        {{ $proposta->cliente->morada }}<br>
                        {{ $proposta->cliente->codigo_postal }} {{ $proposta->cliente->localidade }}
                    </td>
                    <td style="width: 50%; text-align: right;">
                        <strong>EMPRESA:</strong><br>
                        Nome da Sua Empresa<br>
                        NIF da Sua Empresa<br>
                        Morada da Sua Empresa<br>
                        Contacto da Sua Empresa
                    </td>
                </tr>
            </table>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Referência</th>
                    <th>Descrição</th>
                    <th class="text-right">Qtd.</th>
                    <th class="text-right">Preço Unit.</th>
                    <th class="text-right">Taxa IVA</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($proposta->linhas as $linha)
                    <tr>
                        <td>{{ $linha->referencia }}</td>
                        <td>{{ $linha->descricao }}</td>
                        <td class="text-right">{{ number_format($linha->quantidade, 2, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($linha->preco_unitario, 2, ',', '.') }} €</td>
                        <td class="text-right">{{ number_format($linha->taxa_iva, 2, ',', '.') }} %</td>
                        <td class="text-right">
                            {{ number_format($linha->quantidade * $linha->preco_unitario * (1 + $linha->taxa_iva / 100), 2, ',', '.') }}
                            €</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <table>
                <tr>
                    <td>Subtotal:</td>
                    <td class="text-right">{{ number_format($subtotal, 2, ',', '.') }} €</td>
                </tr>
                <tr>
                    <td>Total IVA:</td>
                    <td class="text-right">{{ number_format($totalIva, 2, ',', '.') }} €</td>
                </tr>
                <tr>
                    <td class="font-bold">TOTAL:</td>
                    <td class="text-right font-bold">{{ number_format($proposta->valor_total, 2, ',', '.') }} €</td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>
