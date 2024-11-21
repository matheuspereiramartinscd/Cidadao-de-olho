<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Deputados</title>
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
        <h1>Lista de Deputados</h1>
    </header>

    <div class="container">
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Partido</th>
                    <th>Email</th>
                    <th>Telefone</th>
                </tr>
            </thead>
            <tbody>
                @foreach($deputados as $deputado)
                    <tr>
                        <td>{{ $deputado->nome }}</td>
                        <td>{{ $deputado->partido }}</td>
                        <td>{{ $deputado->email }}</td>
                        <td>{{ $deputado->telefone }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</body>
</html>
