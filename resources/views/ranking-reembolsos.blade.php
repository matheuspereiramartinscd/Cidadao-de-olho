<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ranking de Reembolsos de Deputados</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #007BFF;
            color: white;
            padding: 10px 0;
            text-align: center;
        }

        h1 {
            font-size: 2.5rem;
        }

        .container {
            width: 80%;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .form-label {
            font-size: 1.2rem;
            margin-bottom: 10px;
            display: block;
            color: #333;
        }

        .form-select {
            width: 100%;
            padding: 10px;
            font-size: 1rem;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        table th, table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        table th {
            background-color: #f8f8f8;
            color: #007BFF;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tr:hover {
            background-color: #f1f1f1;
        }

        #ranking {
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <header>
        <h1>Ranking de Reembolsos de Deputados em 2019</h1>
    </header>

    <div class="container">
        <label for="mes" class="form-label">Escolha o Mês</label>
        <select id="mes" class="form-select" onchange="carregarRanking()">
            <option value="janeiro"selected>Janeiro</option>
            <option value="fevereiro">Fevereiro</option>
            <option value="marco">Março</option>
            <option value="abril">Abril</option>
            <option value="maio">Maio</option>
            <option value="junho">Junho</option>
            <option value="julho">Julho</option>
            <option value="agosto">Agosto</option>
            <option value="setembro">Setembro</option>
            <option value="outubro">Outubro</option>
            <option value="novembro">Novembro</option>
            <option value="dezembro">Dezembro</option>
        </select>

        <div id="ranking">
            <table>
                <thead>
                    <tr>
                        <th>Ranking</th>
                        <th>Deputado</th>
                        <th>Total Reembolsado</th>
                    </tr>
                </thead>
                <tbody id="ranking-table">
                    <!-- Dados do ranking serão exibidos aqui -->
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function carregarRanking() {
            const mes = document.getElementById('mes').value;
            const apiUrl = `/ranking-reembolsos-${mes}`;

            fetch(apiUrl)
                .then(response => response.json())
                .then(data => {
                    const table = document.getElementById('ranking-table');
                    table.innerHTML = '';

                    const sortedData = Object.entries(data).sort(([, a], [, b]) => {
                        const valorA = parseFloat(a.total_reembolsado.replace('R$', '').replace('.', '').replace(',', '.'));
                        const valorB = parseFloat(b.total_reembolsado.replace('R$', '').replace('.', '').replace(',', '.'));
                        return valorB - valorA; // Ordem decrescente
                    });

                    if (sortedData.length > 0) {
                        sortedData.forEach(([_, info], index) => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td>${index + 1}</td>
                                <td>${info.deputado.nome}</td>
                                <td>${info.total_reembolsado}</td>
                            `;
                            table.appendChild(row);
                        });
                    } else {
                        table.innerHTML = '<tr><td colspan="3">Nenhum dado encontrado</td></tr>';
                    }
                })
                .catch(error => {
                    console.error('Erro ao carregar os dados:', error);
                    const table = document.getElementById('ranking-table');
                    table.innerHTML = '<tr><td colspan="3">Erro ao carregar os dados</td></tr>';
                });
        }

        // Carregar o ranking ao iniciar a página
        window.onload = carregarRanking;
    </script>

</body>
</html>
