<?php
session_start();

// Função para limpar a sessão ao fazer logout
function logout() {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit();
}

// Verifica se o logout foi solicitado
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['logout'])) {
    logout();
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Minha Loja</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link rel="stylesheet" type="text/css" href="./src/view/assets/css/estilo.css">
</head>
<body>
    <h1>Minha Loja de Produtos</h1>
    <input  type="text" id="searchInput" placeholder="Pesquisar produtos">
    <br>
    <i id="burguer" class="material-symbols-outlined" onclick="clickMenu()">menu</i>
    <menu id="itens">
        <ul>
            <?php if (!isset($_SESSION['login'])) : ?>
            <li><a href="login.php">login</a></li>
            <?php endif; ?>
            <?php if (!isset($_SESSION['login']) || ($_SESSION['tipo_usuario'] == 2)) : ?>
                <li><a href="src/view/carrinho.php">Carrinho</a></li>
            <?php endif; ?>
            <?php if (isset($_SESSION['login']) && $_SESSION['tipo_usuario'] == 1) : ?>
                <li><a href="src/view/cadastrar_produto.php">cadastrar_produto</a></li>
            <?php endif; ?>
            <?php if (isset($_SESSION['login']) && $_SESSION['tipo_usuario'] == 3) : ?>
                <li><a href="cadastro_adm.php">cadastrar user Adm</a></li>
            <?php endif; ?>
            <?php if (isset($_SESSION['login'])) : ?>
                <li>
                    <form method="post" action="index.php">
                        <input type="hidden" name="logout" value="1">
                        <input type="submit" value="Logout" class='button'>
                    </form>
                </li>
                <li>Bem-vindo, <?php echo htmlspecialchars($_SESSION['nome']); ?></li>
                <li><a href="redefinir_senha.php">Redefinir Senha</a></li>
                <li><a href="redefinir_nome.php">Redefinir Nome</a></li>
            <?php endif; ?>
        </ul>
    </menu>
    <div class="container">
        <?php
        // Conexão com o banco de dados
        $host = "localhost";
        $username = "root";
        $password = "";
        $dbname = "ProjetoDEVWEB3";
        $conn = new mysqli($host, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Erro ao conectar ao banco de dados: " . $conn->connect_error);
        }

        $stmt = $conn->prepare("SELECT id, nome, descricao, preco, imagem, slug FROM produto");
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<form method='get' action='./src/view/produto.php'>";
                echo "<input type='hidden' name='slug' value='" . htmlspecialchars($row["slug"]) . "'>";
                echo "<button type='submit' style='border: none; background: none; padding: 0; text-decoration: none; color: inherit;'>";
                echo "<div class='card'>";
                echo "<img src='data:image/jpeg;base64," . base64_encode($row["imagem"]) . "'>";
                echo "<div class='card-content'>";
                echo "<h3>" . htmlspecialchars($row["nome"]) . "</h3>";

                // Truncar a descrição para 20 palavras
                $descricao = explode(' ', $row["descricao"]);
                $descricao = array_slice($descricao, 0, 5);
                $descricao = implode(' ', $descricao);

                echo "<p>" . htmlspecialchars($descricao) . "</p>";
                echo "<p class='price'>R$" . number_format($row["preco"], 2, ',', '.') . "</p>";
                echo "</div>";
                echo "</div>";
                echo "</button>";
                echo "</form>";
            }

        } else {
            echo "Nenhum produto encontrado.";
        }
        $stmt->close();
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
        function clickMenu(){
            if(itens.style.display == 'block'){
                itens.style.display = 'none'
            }else{
                itens.style.display = 'block'
            }
        }
    </script>
    
    
</body>
</html>
