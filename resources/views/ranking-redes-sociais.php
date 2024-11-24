<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ranking das Redes Sociais</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e0e0e0; /* Fundo mais claro */
            color: #333333; /* Texto mais escuro para contraste */
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: center;
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
            margin-top: 140px; /* Ajuste para não sobrepor o header fixo */
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

        #loading {
            font-size: 1.2rem;
            color: #2f2f2f; /* Cor escura para o carregamento */
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    <header>
        <div class="logo-titulo-container">


            <h1>Ranking de Redes Sociais</h1>
        </div>
    </header>

    <div class="container">
        <div id="loading" style="display: none;">Carregando dados...</div>
        <table>
            <thead>
                <tr>
                    <th>Ranking</th> <!-- Adicionada coluna de posição -->
                    <th>Rede Social</th>
                    <th>Total de Deputados</th>
                </tr>
            </thead>
            <tbody id="redes-social-table">
                <!-- Dados das redes sociais serão exibidos aqui -->
            </tbody>
        </table>

        <div class="button-container">
            <a href="/" class="button">Página Principal</a>
            <a href="/deputados" class="button">Lista de Deputados</a>
            <a href="/ranking-reembolsos" class="button">Ranking Reembolsos</a>
        </div>
    </div>

    <script>
        // Fazendo a requisição à API para listar as redes sociais
        fetch('/ranking-redes-sociais/json')
            .then(response => response.json())
            .then(data => {
                const table = document.getElementById('redes-social-table');
                
                // Verificar se a resposta contém dados
                if (data && Array.isArray(data)) {
                    data.forEach((rede, index) => {
                        const row = document.createElement('tr');
                        // Formatando o número de deputados com separadores de milhar
                        const totalDeputados = rede.total ? rede.total.toLocaleString() : 'N/A';
                        row.innerHTML = ` 
                            <td>${index + 1}º</td> <!-- Exibe a posição -->
                            <td>${rede.nome}</td>
                            <td>${totalDeputados}</td>
                        `;
                        table.appendChild(row);
                    });
                } else {
                    table.innerHTML = '<tr><td colspan="3">Nenhum dado encontrado</td></tr>';
                }
            })
            .catch(error => {
                console.error('Erro ao carregar os dados:', error);
                const table = document.getElementById('redes-social-table');
                table.innerHTML = '<tr><td colspan="3">Erro ao carregar os dados</td></tr>';
            });
    </script>

</body>
</html>

