<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit();
}
?>
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

        .btn-cadastrar {
            display: none;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <h1>Minha Loja de Produtos</h1>
    <input style="width: 800px" type="text" id="searchInput" placeholder="Pesquisar produtos">
    <br>

    <?php if (!isset($_SESSION['login'])) : ?>
        <a href="login.php">login<i class="fas fa-user"></i></a>
        <br>
    <?php endif; ?>
    <?php if (!isset($_SESSION['login']) || ($_SESSION['tipo_usuario'] == 2)) : ?>
        <a href="src/view/carrinho.php">Carrinho<i class="fas fa-user"></i></a>
        <br>
    <?php endif; ?>

    <?php if (isset($_SESSION['login']) && $_SESSION['tipo_usuario'] == 1) : ?>
        <a href="src/view/cadastrar_produto.php">cadastrar_produto</a>
    <?php endif; ?>
    <?php if (isset($_SESSION['login']) && $_SESSION['tipo_usuario'] == 3) : ?>
        <a href="cadastro_adm.php">cadastrar user Adm</a>
    <?php endif; ?>

    <?php if (isset($_SESSION['login'])) : ?>
        <form method="post" action="index.php">
            <input type="hidden" name="logout" value="1">
            <input type="submit" value="Logout">
        </form>
        <br>

        <span>Bem-vindo, <?php echo $_SESSION['nome']; ?></span>
        <br>

        <a href="redefinir_senha.php">Redefinir Senha</a>
        <a href="redefinir_nome.php">Redefinir Nome</a>

    <?php endif; ?>


    <div class="container">
        <?php
        // Conexão com o banco de dados
        $host = "localhost"; // host do banco de dados
        $username = "root"; // nome de usuário do banco de dados
        $password = ""; // senha do banco de dados
        $dbname = "ProjetoDEVWEB3"; // nome do banco de dados
        $conn = new mysqli($host, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Erro ao conectar ao banco de dados: " . $conn->connect_error);
        }

        $sql = "SELECT id, nome, descricao, preco, imagem FROM produto";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<form method='post' action='./src/view/produto.php'>";
                echo "<input type='hidden' name='id' value='" . $row["id"] . "'>";
                echo "<button type='submit' style='border: none; background: none; padding: 0; text-decoration: none; color: inherit;'>";
                echo "<div class='card'>";
                echo "<img src='data:image/jpeg
                ;base64," . base64_encode($row["imagem"]) . "'>";
                echo "<div class='card-content'>";
                echo "<h3>" . $row["nome"] . "</h3>";
                echo "<p>" . $row["descricao"] . "</p>";
                echo "<p class='price'>R$" . number_format($row["preco"], 2, ',', '.') . "</p>";
                echo "</div>";
                echo "</div>";
                echo "</button>";
                echo "</form>";
            }
        } else {
            echo "Nenhum produto encontrado.";
        }
        $conn->close();
        ?>
    </div>

    <script>
        document.getElementById("searchInput").addEventListener("input", function() {
            var input, filter, cards, card, i, txtValue;
            input = document.getElementById("searchInput");
            filter = input.value.toUpperCase();
            cards = document.getElementsByClassName("card");
            for (i = 0; i < cards.length; i++) {
                card = cards[i];
                txtValue = card.textContent || card.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    card.style.display = "";
                } else {
                    card.style.display = "none";
                }
            }
        });
    </script>
</body>

</html>