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
    </style>
</head>
<body>
    <h1>Minha Loja de Produtos</h1>

    <div class="container">
        <?php
        $host = "localhost"; // host do banco de dados
        $username = "root"; // nome de usuário do banco de dados
        $password = ""; // senha do banco de dados
        $dbname = "ProjetoDEVWEB3"; // nome do banco de dados

        // Conexão com o banco de dados
        $conn = new mysqli($host, $username, $password, $dbname);

        // Verifica se houve algum erro na conexão
        if ($conn->connect_error) {
            die("Erro ao conectar ao banco de dados: " . $conn->connect_error);
        }

        $sql = "SELECT id, nome, descricao, preco, imagem FROM produto";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<a href='./src/view/produto.php?id=" . $row["id"] . "' style='text-decoration: none; color: inherit;'>";
                echo "<div class='card'>";
                echo "<img src='data:image/jpeg;base64," . base64_encode($row["imagem"]) . "'>";
                echo "<div class='card-content'>";
                echo "<h3>" . $row["nome"] . "</h3>";
                echo "<p>" . $row["descricao"] . "</p>";
                echo "<p class='price'>R$" . number_format($row["preco"], 2, ',', '.') . "</p>";
                echo "</div>";
                echo "</div>";
                echo "</a>";
            }
        } else {
            echo "Nenhum produto encontrado.";
        }

        $conn->close();
        ?>
    </div>
</body>
</html>
