<!DOCTYPE html>
<html>
<head>
    <title>Minha Loja</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        h1 {
            text-align: center;
            padding: 20px 0;
            background-color: #333;
            color: white;
            margin: 0;
        }
        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
        }
        .card {
            width: 200px;
            background-color: white;
            border-radius: 5px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.3s;
        }
        .card:hover {
            transform: scale(1.05);
        }
        .card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }
        .card-content {
            padding: 15px;
        }
        .card-content h3 {
            margin: 0;
            font-size: 16px;
            color: #333;
        }
        .card-content p {
            margin: 10px 0;
            color: #666;
            font-size: 14px;
            line-height: 1.4;
            max-height: 3.2em;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .card-content .price {
            font-weight: bold;
            color: #333;
            font-size: 16px;
        }
        #searchInput {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <h1>Minha Loja de Produtos</h1>

    <!-- Formulário de pesquisa -->
    <form id="searchForm" method="GET">
        <input type="text" id="searchInput" name="q" placeholder="Pesquisar...">
    </form>

    <div class="container" id="productsContainer">
        <!-- Resultados da pesquisa serão exibidos aqui -->
    </div>

    <script>
        // Função para atualizar os produtos exibidos com base na pesquisa
        function updateProducts() {
            var searchQuery = document.getElementById('searchInput').value;
            var url = 'search.php?q=' + encodeURIComponent(searchQuery);

            fetch(url)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('productsContainer').innerHTML = data;
                });
        }

        // Adiciona um ouvinte de eventos ao campo de pesquisa para atualizar os produtos enquanto o usuário digita
        document.getElementById('searchInput').addEventListener('input', function(event) {
            updateProducts(); // Atualiza os produtos exibidos
        });

        // Atualiza os produtos iniciais ao carregar a página
        updateProducts();
    </script>
</body>
</html>
