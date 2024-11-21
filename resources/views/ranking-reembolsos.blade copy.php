<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ranking de Reembolsos</title>
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

        .form-label {
            font-weight: bold;
            margin-bottom: 10px;
            display: block;
        }

        .form-select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            margin-bottom: 20px;
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

        p {
            font-size: 1rem;
            text-align: center;
            margin: 20px 0;
        }
    </style>
</head>
<body>

    <header>
        <h1>Ranking de Reembolsos de Deputados</h1>
    </header>

    <div class="container">
        <label for="mes" class="form-label">Escolha o Mês</label>
        <select id="mes" class="form-select" onchange="carregarRanking()">
            <option value="janeiro">Janeiro</option>
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
            <!-- Os dados dos deputados serão carregados aqui -->
        </div>
    </div>

    <script>
        // Função para carregar os dados do ranking com base no mês escolhido
        function carregarRanking() {
            const mesSelecionado = document.getElementById('mes').value;
            const url = `/ranking-reembolsos-${mesSelecionado}`; // URL dinâmica com o mês selecionado

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    const rankingContainer = document.getElementById('ranking');
                    rankingContainer.innerHTML = ''; // Limpar o conteúdo anterior

                    const deputados = Object.values(data); // Converte os valores do objeto indexado em um array

                    if (deputados.length > 0) {
                        let html = '<table>';
                        html += '<thead><tr><th>Ranking</th><th>Deputado</th><th>Total Reembolsado</th></tr></thead>';
                        html += '<tbody>';
                        deputados.forEach((deputado, index) => {
                            // Limitar a exibição para os top 5 deputados
                            if (index < 5) {
                                const nome = deputado.deputado ? deputado.deputado.nome : 'Nome não disponível';
                                const totalReembolsado = deputado.total_reembolsado || 'R$ 0,00';
                                html += `<tr><td>${index + 1}</td><td>${nome}</td><td>${totalReembolsado}</td></tr>`;
                            }
                        });
                        html += '</tbody></table>';
                        rankingContainer.innerHTML = html;
                    } else {
                        rankingContainer.innerHTML = '<p>Nenhum dado encontrado para este mês.</p>';
                    }
                })
                .catch(error => {
                    console.error('Erro ao carregar os dados:', error);
                    alert('Ocorreu um erro ao carregar os dados');
                });
        }

        // Carregar o ranking inicial de janeiro
        carregarRanking();
    </script>

</body>
</html>
