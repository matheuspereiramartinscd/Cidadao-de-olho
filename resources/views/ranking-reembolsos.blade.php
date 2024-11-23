<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ranking de Reembolsos de Deputados</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e0e0e0; /* Fundo mais claro */
            color: #333333; /* Texto mais escuro para contraste */
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            flex-direction: column;
        }

        header {
            background-color: #212121; /* Cor escura */
            color: #FFD700; /* Dourado para destaque */
            padding: 15px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.7);
            width: 100%;
        }

        .logo-titulo-container {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logo {
            width: 50px;
            height: 50px;
        }

        h1 {
            font-size: 2rem;
            margin: 0;
        }

        .container {
            width: 80%;
            margin-top: -180px;
            padding: 30px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
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
            background-color: #2f2f2f; /* Azul escuro para o cabeçalho */
            color: #ffffff; /* Texto branco */
            font-size: 1.1rem;
        }

        table tr:nth-child(even) {
            background-color: #f7f7f7; /* Cor clara para linhas alternadas */
        }

        table tr:hover {
            background-color: #e6e6e6; /* Cor para hover nas linhas */
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
            color: #2f2f2f;
            text-align: center;
            margin-bottom: 20px;
        }

        .button-container {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 50px;
        }

        .button {
            background-color: #FFD700;
            color: #212121;
            padding: 15px 25px;
            text-align: center;
            text-decoration: none;
            font-size: 1.2rem;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease, box-shadow 0.2s ease;
        }

        .button:hover {
            background-color: #e6c200; /* Tom mais escuro de dourado */
            transform: scale(1.05);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>

    <header>
        <div class="logo-titulo-container">

            <h1 class="titulo">Ranking de Reembolsos de Deputados</h1>
        </div>
    </header>

    <div class="container">
        <label for="mes" class="form-label">Escolha o Mês</label>
        <select id="mes" class="form-select" onchange="carregarRanking()">
            <option value="" disabled selected>Selecione um mês</option>
            <option value="1">Janeiro</option>
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

        <div class="button-container">
        <a href="/" class="button">Página Principal</a>
            <a href="/deputados" class="button">Lista de Deputados</a>
            <a href="/ranking-redes-sociais" class="button">Ranking Redes Sociais</a>
        </div>
    </div>

    <script>
        function carregarRanking() {
            const mes = document.getElementById('mes').value;

            // Verificar se uma opção válida foi selecionada
            if (!mes) {
                const table = document.getElementById('ranking-table');
                table.innerHTML = '<tr><td colspan="3">Selecione um mês para exibir os dados.</td></tr>';
                return;
            }

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

                    const deputados = Object.values(data);

                    deputados.sort((a, b) => {
                        const totalA = parseFloat(a.total_reembolsado.replace('R$ ', '').replace('.', '').replace(',', '.'));
                        const totalB = parseFloat(b.total_reembolsado.replace('R$ ', '').replace('.', '').replace(',', '.'));
                        return totalB - totalA;
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
