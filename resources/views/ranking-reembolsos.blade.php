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

        .form-select {
            padding: 8px;
            font-size: 16px;
            margin-bottom: 15px;
            border-radius: 4px;
            border: 1px solid #ccc;
            width: 100%;
        }

        #loading {
            font-size: 1.2rem;
            color: #007BFF;
            text-align: center;
            margin-bottom: 20px;
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
            <option value="1" selected>Janeiro</option>
            <option value="2">Fevereiro</option>
            <option value="3">Março</option>
            <option value="4">Abril</option>
            <option value="5">Maio</option>
            <option value="6">Junho</option>
            <option value="7">Julho</option>
            <option value="8">Agosto</option>
            <option value="9">Setembro</option>
            <option value="10">Outubro</option>
            <option value="11">Novembro</option>
            <option value="12">Dezembro</option>
        </select>

        <div id="ranking">
            <p id="loading" style="display: none;">Carregando dados...</p>
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
            const apiUrl = `/ranking-reembolsos/${mes}`;

            // Exibir mensagem de carregamento
            const loadingElement = document.getElementById('loading');
            loadingElement.style.display = 'block';

            fetch(apiUrl)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Erro: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    const table = document.getElementById('ranking-table');
                    table.innerHTML = '';

                    // Usar Object.values para acessar os valores do objeto retornado
                    const deputados = Object.values(data);

                    // Ordenar os deputados pelo total reembolsado de forma decrescente
                    deputados.sort((a, b) => {
                        // Converter os valores para números, considerando a vírgula como separador decimal
                        const totalA = parseFloat(a.total_reembolsado.replace('R$ ', '').replace('.', '').replace(',', '.'));
                        const totalB = parseFloat(b.total_reembolsado.replace('R$ ', '').replace('.', '').replace(',', '.'));
                        return totalB - totalA; // Ordenação decrescente
                    });

                    if (deputados.length > 0) {
                        deputados.forEach((item, index) => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td>${index + 1}</td>
                                <td>${item.deputado.nome}</td>
                                <td>R$ ${item.total_reembolsado}</td>
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
                })
                .finally(() => {
                    loadingElement.style.display = 'none';
                });
        }

        // Carregar o ranking ao iniciar a página
        window.onload = carregarRanking;
    </script>

</body>
</html>
