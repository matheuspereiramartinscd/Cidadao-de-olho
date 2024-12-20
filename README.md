# Cidadão de Olho - Plataforma de Monitoramento de Gastos Públicos

Este projeto é uma **prova de conceito** desenvolvida para atender à necessidade de monitorar os gastos públicos em verbas indenizatórias na Assembleia Legislativa de Minas Gerais (ALMG). O objetivo é facilitar o acesso a dados sobre o uso de recursos pelos deputados estaduais e proporcionar uma maneira interativa e acessível para a população acompanhar esses gastos.

A plataforma também oferece insights sobre o impacto das redes sociais na divulgação desses dados, permitindo um melhor entendimento sobre os canais mais utilizados pelos parlamentares.




## Funcionalidades

### Principais Recursos
- **Monitoramento de Reembolsos:**
  - Exibe os **5 deputados mais gastadores** por mês, com dados exclusivos do ano de 2019.
- **Ranking de Redes Sociais:**
  - Apresenta a classificação das redes sociais mais utilizadas pelos deputados, ordenadas de forma decrescente.
- **Interface de API JSON:**
  - Disponibiliza os dados de forma estruturada para fácil integração com outros sistemas.






## Tecnologias Utilizadas

### Backend
- **PHP**: Linguagem principal para desenvolvimento do sistema.
- **Laravel**: Framework PHP usado para criar APIs robustas e bem estruturadas.
- **GuzzleHTTP**: Biblioteca para realizar chamadas à API pública da ALMG.
- **Migrations e Seeders**: Ferramentas do Laravel para criação e manipulação do banco de dados.

### Banco de Dados
- **MySQL**: Banco relacional escolhido para armazenar os dados dos deputados e redes sociais.




## Funcionalidades Técnicas

1. **Consumo de API Pública:**
   - Integração com a API pública da ALMG para buscar dados de deputados em exercício e seus reembolsos:
     - Deputados em exercício: [API Deputados em Exercício](http://dadosabertos.almg.gov.br/ws/deputados/em_exercicio).
     - Reembolsos: [API de Verbas Indenizatórias](http://dadosabertos.almg.gov.br/ws/ajuda/sobre).
   - Os dados são armazenados localmente para otimização e consultas rápidas.

2. **Endpoints Criados:**
   - **Top 5 Deputados Gastadores (2019):**
     - Endpoint: `/ranking-reembolsos/{mês}`
     - Retorna o ranking mensal de deputados que mais solicitaram reembolsos.
   - **Ranking de Redes Sociais:**
     - Endpoint: `/ranking-redes-sociais`
     - Retorna o uso das redes sociais por deputados, ordenado por frequência de uso.



## Estrutura do Projeto

### Organização
- **Controllers:** Gerenciam as regras de negócio e chamadas para o banco de dados.
- **Models:** Representam as entidades principais, como `Deputado` e `Reembolso`.
- **Migrations:** Criam as tabelas `deputados` e `redes_sociais` no banco de dados.
- **Seeders:** Popularam as tabelas iniciais com dados da API pública.
- **Rotas:** Rotas para acessar os dados.



## Instruções de Instalação e Execução

### Requisitos
- PHP >= 8.2
- Composer
- MySQL
- Node.js >= 16


### Passo a Passo
- Instale o xampp
- Adicione a pasta do php no PATH do windows
- Baixe o arquivo cacert.pem do site oficial: https://curl.se/ca/cacert.pem
- Salve o arquivo em um local seguro, como C:\php\extras\ssl\cacert.pem
- Abra o php.ini e substitua curl.cainfo = "C:\php\extras\ssl\cacert.pem" e openssl.cafile="C:\php\extras\ssl\cacert.pem"
- Altere max_execution_time = 30 para max_execution_time = 300
- Altere ;extension=pdo_sqlite para extension=pdo_sqlite
- Altere extension=pdo_firebird para ;extension=pdo_firebird
- Caso for preciso rode o comando php artisan key:generate

1. **Clone o Repositório:**

       git clone https://github.com/matheuspereiramartinscd/Cidadao-de-olho.git
    
       cd cidadao-de-olho

2. **Instale as dependências do backend e frontend:**

        composer install
    
        npm install

3. **Configure o Banco de Dados**
        Crie um banco de dados no MySQL (por exemplo, cidadao_de_olho).
        
        Configure as credenciais do banco de dados no arquivo .env:
        
        DB_CONNECTION=mysql
        
        DB_HOST=127.0.0.1
        
        DB_PORT=3306
        
        DB_DATABASE=cidadao_de_olho
        
        DB_USERNAME=root
        
        DB_PASSWORD=

4. **Execute as Migrations e Seeders**

        php artisan migrate

5. **Inicie o Servidor**

        php artisan serve

6. **Acesse a API**

        Após iniciar o servidor, acesse a rota http:127.0.0.1:8000 para entrar na página inicial e clique no botão Obter Dados dos Deputados para carregar e salvar os dados no banco de dados


</br>

![Screenshot_1](https://github.com/user-attachments/assets/f732eb1c-af16-4744-8ebf-4e4bcefcea35)


</br>

![Screenshot_2](https://github.com/user-attachments/assets/ca6c899c-aa01-49b9-882d-f2feb09a0e66)


</br>

![Screenshot_3](https://github.com/user-attachments/assets/c3807a29-9d28-4ff2-9359-183115942433)


</br>

![Screenshot_4](https://github.com/user-attachments/assets/05ef5bba-e937-42ba-aeaa-f8d2903f2369)



