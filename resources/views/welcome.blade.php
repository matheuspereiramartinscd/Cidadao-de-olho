<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Escolha a Opção</title>
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

        .fetch-button {
            background-color: #26805E; /* Verde escuro, alinhado com o tema */
            color: white;
            font-size: 1rem;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .fetch-button:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
        }

        .main-container {
            background-color: #ffffff; /* Fundo claro para a caixa principal */
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 800px;
            text-align: center;
            margin-top: -100px;
        }

        .button-container {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }

        .button {
            background-color: #FFD700; /* Dourado para as opções */
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

        .message {
            margin-top: 20px;
            font-size: 1.2rem;
            padding: 10px;
            text-align: center;
            border-radius: 5px;
            display: none;
        }

        .message.success {
            color: #155724;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
        }

        .message.error {
            color: #721c24;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
        }

        .welcome-message {
            background-color: ##402323; /* Fundo claro */
            color: #212121; /* Texto escuro para contraste */
            font-size: 1.5rem;
            padding: 20px;
            margin-top: 20px;
            border-radius: 8px;
   
            text-shadow: 1px 1px 5px rgba(0, 0, 0, 0.1); /* Sombra leve */
        }
    </style>
</head>

<body>
    <header>
        <div class="logo-titulo-container">
            <img src="{{ asset('logo/magnifier.png') }}" alt="Logo Cidadão de Olho" class="logo">
            <h1 class="titulo">Cidadão de Olho</h1>
        </div>
        <button class="fetch-button" id="fetch-data">Obter Dados dos Deputados</button>
    </header>

    <div class="main-container">
        <div class="welcome-message">
            <p>Bem-vindo ao sistema Cidadão de Olho! Aqui você pode acompanhar dados e rankings sobre deputados e reembolsos. Selecione uma das opções abaixo para começar.</p>
        </div>

        <div class="button-container">
            <a href="/deputados" class="button">Lista de Deputados</a>
            <a href="/ranking-redes-sociais" class="button">Ranking Redes Sociais</a>
            <a href="/ranking-reembolsos" class="button">Ranking de Reembolsos</a>
        </div>

        <div id="message" class="message"></div>
    </div>

    <script>
        document.getElementById('fetch-data').addEventListener('click', function () {
            const messageElement = document.getElementById('message');
            messageElement.style.display = 'none'; 

            messageElement.textContent = 'Obtendo dados, por favor aguarde...';
            messageElement.className = 'message';
            messageElement.style.display = 'block';

            fetch('/get-deputados', { method: 'GET' })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Erro ao acessar a API: ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(data => {
                    messageElement.textContent = 'Os dados foram obtidos e salvos no banco com sucesso!';
                    messageElement.className = 'message success';
                })
                .catch(error => {
                    messageElement.textContent = `Erro ao obter os dados: ${error.message}`;
                    messageElement.className = 'message error';
                });
        });
    </script>
</body>
</html>
