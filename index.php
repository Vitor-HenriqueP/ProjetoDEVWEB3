<?php
session_start();

// Função para limpar a sessão ao fazer logout
function logout()
{
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
    <title>Estação Digital | Preço baixo e entrega expressa !</title>
    <link rel="stylesheet" type="text/css" href="./src/view/assets/css/style.css">


 

</head>

<body id="body">
    <div class="header" id="header">
        <button onclick="toggleSidebar()" class="btn_icon_header">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-list" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z" />
            </svg>
        </button>
        <div class="logo_header">
            <a href="index.php"><img src="./src/view/assets/imagens/newlogo.png" alt="Logo Estação Digital" class="img_logo_header"></a>
        </div>
        <div class="navigation_header" id="navigation_header">
            <button onclick="toggleSidebar()" class="btn_icon_header">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                    <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z" />
                </svg>
            </button>
            <?php if (!isset($_SESSION['login'])) : ?>
                <a href="login.php">login</a>
            <?php endif; ?>
            <div class="pesquisar">
                <input type="text" placeholder="Busque aqui" id="searchInput" class="txtpesquisar" />
                <a class="btpesquisar">
                    <img class="lupa-branca" src="./src/view/assets/imagens/lupa-branca.svg" alt="Pesquisar" width="25px" height="25px" />
                </a>
            </div>

            <?php if (isset($_SESSION['login'])) : ?>
                <form method="post" action="index.php">
                    <input type="hidden" name="logout" value="1">
                    <input type="submit" value="Logout" class='button'>
                </form>
            <?php endif; ?>

        </div>
    </div>
    
    <nav class="nav1">
        <?php if (isset($_SESSION['login'])) : ?>
            <p>Bem-vindo, <?php echo htmlspecialchars($_SESSION['nome']); ?></p>

            <?php if (isset($_SESSION['login']) && $_SESSION['tipo_usuario'] == 1) : ?>
                <a href="src/view/cadastrar_produto.php">Cadastrar produto</a>
            <?php endif; ?>
            <?php if (!isset($_SESSION['login']) || ($_SESSION['tipo_usuario'] == 2)) : ?>
                <a href="src/view/carrinho.php">Carrinho</a>
            <?php endif; ?>
            <a href="redefinir_senha.php">Redefinir Senha</a></li>
            <a href="redefinir_nome.php">Redefinir Nome</a></li>
        <?php endif; ?>
        <?php if (isset($_SESSION['login']) && $_SESSION['tipo_usuario'] == 3) : ?>
            <a href="cadastro_adm.php">cadastrar user Adm</a>
            </nav>
<nav>
        <?php endif; ?>
        <a href="#" class="categoria" data-categoria="Todas">Todas</a>
        <a href="#" class="categoria" data-categoria="Eletrônicos">Eletrônicos</a>
        <a href="#" class="categoria" data-categoria="Roupas">Roupas</a>
        <a href="#" class="categoria" data-categoria="Acessórios">Acessórios</a>

    </nav>
    <div class="containerOferta">
        <span class="oferta">FRETE GRÁTIS PARA COMPRAS ACIMA DE R$99,99!!!</span>
    </div>
    </div>
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

        $stmt = $conn->prepare("SELECT id, nome, descricao, preco, imagem, slug, categoria FROM produto");
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<form method='get' action='./src/view/produto.php'>";
                echo "<input type='hidden' name='slug' value='" . htmlspecialchars($row["slug"]) . "'>";
                echo "<button type='submit' style='border: none; background: none; padding: 0; text-decoration: none; color: inherit;'>";
                echo "<div class='card " . htmlspecialchars($row["categoria"]) . "'>";
                
                echo "<img src='data:image/jpeg;base64," . base64_encode($row["imagem"]) . "'>";
                echo "<div class='card-content'>";
                echo "<h3>" . htmlspecialchars($row["nome"]) . "</h3>";

                // Truncar a descrição para 20 palavras
                $descricao = explode(' ', $row["descricao"]);
                $descricao = array_slice($descricao, 0, 5);
                $descricao = implode(' ', $descricao);

                echo "<p>" . htmlspecialchars($descricao) . "</p>";
                echo "<p class='hidden'>" . htmlspecialchars($row["categoria"]) . "</p>";

                echo "<p class='price'>R$" . number_format($row["preco"], 2, ',', '.') . "</p>";
                echo "</div>";
                echo "</div>";
            }
        } else {
            echo "Nenhum produto encontrado.";
        }
        $stmt->close();
        $conn->close();
        ?>
    </div>

    <script>
        document.querySelectorAll('.categoria').forEach(item => {
            item.addEventListener('click', event => {
                const categoria = event.target.getAttribute('data-categoria');
                const cards = document.querySelectorAll('.card');

                cards.forEach(card => {
                    if (categoria === 'Todas' || card.classList.contains(categoria)) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });

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

        var header = document.getElementById('header');
        var navigationHeader = document.getElementById('navigation_header');
        var content = document.getElementById('content');
        var showSidebar = false;

        function toggleSidebar() {
            showSidebar = !showSidebar;
            if (showSidebar) {
                navigationHeader.style.marginLeft = '-10vw';
                navigationHeader.style.animationName = 'showSidebar';
                content.style.filter = 'blur(2px)';
            } else {
                navigationHeader.style.marginLeft = '-100vw';
                navigationHeader.style.animationName = '';
                content.style.filter = '';
            }
        }

        function closeSidebar() {
            if (showSidebar) {
                showSidebar = true;
                toggleSidebar();
            }
        }

        window.addEventListener('resize', function(event) {
            if (window.innerWidth > 768 && showSidebar) {
                showSidebar = true;
                toggleSidebar();
            }
        });

        function clickMenu() {
            if (itens.style.display == 'block') {
                itens.style.display = 'none'
            } else {
                itens.style.display = 'block'
            }
        }
    </script>

</body>

</html>