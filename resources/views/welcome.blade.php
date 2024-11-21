<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deputados</title>
</head>
<body>
    <h1>Lista de Deputados</h1>
    <table>
        <thead>
            <tr>
                <th>Nome</th>
                <th>Partido</th>
                <th>Email</th>
                <th>Telefone</th>
            </tr>
        </thead>
        <tbody id="deputados-table">
            <!-- Deputados will be populated here -->
        </tbody>
    </table>

    <script>
        fetch('/get-deputados')
            .then(response => response.json())
            .then(data => {
                const deputadosTable = document.getElementById('deputados-table');
                data.forEach(deputado => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${deputado.nome}</td>
                        <td>${deputado.partido}</td>
                        <td>${deputado.email}</td>
                        <td>${deputado.telefone}</td>
                    `;
                    deputadosTable.appendChild(row);
                });
            });
    </script>
</body>
</html>
