<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ranking das Redes Sociais</title>
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
    </style>
</head>
<body>

    <header>
        <h1>Ranking de Redes Sociais</h1>
    </header>

    <div class="container">
        <table>
            <thead>
                <tr>
                    <th>Rede Social</th>
                    <th>Total de Deputados</th>
                </tr>
            </thead>
            <tbody id="redes-social-table">
                <!-- Dados das redes sociais serão exibidos aqui -->
            </tbody>
        </table>
    </div>

    <script>
        // Fazendo a requisição à API para listar as redes sociais
        fetch('/ranking-redes-sociais')
            .then(response => response.json())
            .then(data => {
                const table = document.getElementById('redes-social-table');
                
                // Verificar se a resposta contém dados
                if (data && Array.isArray(data)) {
                    data.forEach(rede => {
                        const row = document.createElement('tr');
                        // Formatando o número de deputados com separadores de milhar
                        const totalDeputados = rede.total ? rede.total.toLocaleString() : 'N/A';
                        row.innerHTML = `
                            <td>${rede.nome}</td>
                            <td>${totalDeputados}</td>
                        `;
                        table.appendChild(row);
                    });
                } else {
                    table.innerHTML = '<tr><td colspan="2">Nenhum dado encontrado</td></tr>';
                }
            })
            .catch(error => {
                console.error('Erro ao carregar os dados:', error);
                const table = document.getElementById('redes-social-table');
                table.innerHTML = '<tr><td colspan="2">Erro ao carregar os dados</td></tr>';
            });
    </script>

</body>
</html>
