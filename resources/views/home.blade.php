<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Escolha a Opção</title>
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
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .button {
            background-color: #007BFF;
            color: white;
            padding: 15px 25px;
            text-align: center;
            text-decoration: none;
            font-size: 1.2rem;
            margin: 10px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .button:hover {
            background-color: #0056b3;
        }

        .button-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Bem-vindo ao sistema Cidadão de Olho</h1>
    </header>

    <div class="container">
        <h2>Escolha uma opção:</h2>
        <div class="button-container">
            <a href="/deputados" class="button">Lista de Deputados</a>
            <a href="/ranking-redes-sociais-front" class="button">Ranking Redes Sociais</a>
            <a href="/ranking-reembolsos" class="button">Ranking de Reembolsos</a>
        </div>
    </div>
</body>
</html>
