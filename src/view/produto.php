<?php
session_start();

include '../../conexao.php'; // Inclua o arquivo de conexão

// Verifica se o slug do produto foi enviado via GET
if (isset($_GET['slug'])) {
    $slug = $_GET['slug'];

    // Consulta SQL para obter os detalhes do produto com base no slug
    $sql = "SELECT * FROM produto WHERE slug = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $slug);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
?>
        <!DOCTYPE html>
        <html lang="pt-br">

        <head>
            <meta charset="UTF-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?php echo htmlspecialchars($row['nome']); ?></title>
            <link rel="stylesheet" type="text/css" href="./assets/css/styleproduct.css">
            <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        </head>

        <body>
            <div class="header" id="header">
                <button onclick="toggleSidebar()" class="btn_icon_header">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-list" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z" />
                    </svg>
                </button>
                <div class="logo_header">
                    <a href="index.php"><img src="./assets/imagens/newlogo.png" alt="Logo Estação Digital" class="img_logo_header"></a>
                </div>
                <div class="navigation_header" id="navigation_header">
                    <button onclick="toggleSidebar()" class="btn_icon_header">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                            <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z" />
                        </svg>
                    </button>
                    <?php if (!isset($_SESSION['login'])) : ?>
                        <a href="login.php"><span class="material-symbols-outlined">login</span></a>
                    <?php endif; ?>
                    <div class="pesquisar">
                        <input type="text" placeholder="Busque aqui" id="searchInput" class="txtpesquisar" />
                        <a class="btpesquisar">
                            <img class="lupa-branca" src="./assets/imagens/lupa-branca.svg" alt="Pesquisar" width="25px" height="25px" />
                        </a>
                    </div>
                    <?php if (!isset($_SESSION['login']) || ($_SESSION['tipo_usuario'] == 2)) : ?>
                        <a href="src/view/carrinho.php"><span class="material-symbols-outlined">shopping_cart</span></a>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['login'])) : ?>
                        <a href="user_config.php"><span class="material-symbols-outlined">person</span></a>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['login'])) : ?>
                        <form method="post" action="index.php">
                            <input type="hidden" name="logout" value="1">
                            <button type="submit" class="button" style="background: none; border: none; cursor: pointer;">
                                <a><span class="material-symbols-outlined" style="vertical-align: middle;">logout</span></a>
                            </button>
                        </form>

                    <?php endif; ?>

                </div>
            </div>

            <nav>
                <?php if (isset($_SESSION['login'])) : ?>
                    <p>Bem-vindo, <?php echo htmlspecialchars($_SESSION['nome']); ?></p>

                    <?php if (isset($_SESSION['login']) && $_SESSION['tipo_usuario'] == 1) : ?>
                        <a href="src/view/cadastrar_produto.php" class="categoria">Cadastrar produto</a>
                    <?php endif; ?>

                <?php endif; ?>
                <?php if (isset($_SESSION['login']) && $_SESSION['tipo_usuario'] == 3) : ?>
                    <a href="cadastro_adm.php" class="categoria">Cadastrar novo administrador</a>
                <?php endif; ?>
                <a href="#" class="categoria" data-categoria="Todas">Todas</a>
                <a href="#" class="categoria" data-categoria="Eletrônicos">Eletrônicos</a>
                <a href="#" class="categoria" data-categoria="Roupas">Roupas</a>
                <a href="#" class="categoria" data-categoria="Acessórios">Acessórios</a>

            </nav>
            <div class="container">
                <div class="product">
                    <div class="product-image">
                        <img src="data:image/jpeg;base64,<?php echo base64_encode($row['imagem']); ?>" alt="<?php echo htmlspecialchars($row['nome']); ?>">
                    </div>
                    <div class="product-info">
                        <h1 class="product-title"><?php echo htmlspecialchars($row['nome']); ?></h1>
                        <p class="product-price">R$<?php echo number_format($row['preco'], 2, ',', '.'); ?></p>


                        <?php
                        // Verifica se o usuário está logado antes de mostrar os botões de editar e excluir
                        if (isset($_SESSION['login'])) {
                            // Formulário para adicionar ao carrinho
                            if ($_SESSION['tipo_usuario'] == 2) {
                                echo "<form method='post' action='../../adicionar_carrinho.php' onsubmit='return checkLogin()'>";
                                echo "<input type='hidden' name='id_produto' value='{$row['id']}'>";
                                echo "<input type='submit' value='Adicionar ao Carrinho' class='buttonAdd'>";
                                echo "</form>"; ?>
                    </div>
                </div>
            </div>
            <div class="containerDesc">
                <label class="toggle-label arrow-down" onclick="toggleDescription()">Descrição</label>
                <p class="product-description"><?php echo htmlspecialchars($row['descricao']); ?></p>
            </div>
            <div class="container">
        <?php
                                // Formulário para adicionar comentário
                                echo "<form id='form1' method='post' action='inserir.php'>";
                                echo "<input type='hidden' name='id_produto' value='{$row['id']}'>";
                                echo "<label for='comment'>Comentário</label><br>";
                                echo "<textarea name='comentario' id='comment' required></textarea><br><br>";
                                echo "<input type='submit' name='enviar_comentario' value='Enviar Comentário'>";
                                echo "</form>";
                            }
                            // Botões de editar e excluir (para usuários do tipo 1)
                            if ($_SESSION['tipo_usuario'] == 1) {
                                echo "<form method='post' action='editar_produto.php'>";
                                echo "<input type='hidden' name='id' value='{$row['id']}'>";
                                echo "<input type='submit' value='Editar'>";
                                echo "</form>";

                                echo "<form method='post' action='excluir_produto.php'>";
                                echo "<input type='hidden' name='id' value='{$row['id']}'>";
                                echo "<input type='submit' value='Excluir' onclick='return confirm(\"Tem certeza que deseja excluir este produto?\")'>";
                                echo "</form>";
                            }

                            // Div para carregar os comentários
                            echo "<div id='comentarios'></div>";
                        } else {
                            // Mostra mensagem e redireciona para a página de login
                            echo "<p>Faça login para adicionar ao carrinho ou comentar</p>";
                            echo "<button onclick='redirectToLogin()'>Login</button>";
                        }
        ?>
            </div>
            <script>
                // Função para enviar o formulário via AJAX
                $('#form1').submit(function(e) {
                    e.preventDefault(); // Evita o envio tradicional do formulário
                    $.ajax({
                        type: 'POST',
                        url: $(this).attr('action'),
                        data: $(this).serialize(),
                        success: function(response) {
                            if (response === 'Comentário Salvo com Sucesso') {
                                // Recarrega os comentários após o envio bem-sucedido
                                loadComentarios(<?php echo $row['id']; ?>);
                                // Limpa o campo de comentário após o envio bem-sucedido
                                $('#comment').val('');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error(xhr.responseText); // Exibe a mensagem de erro no console
                            alert('Erro ao enviar o comentário'); // Exibe uma mensagem de erro genérica
                        }
                    });
                });

                // Função para carregar os comentários via AJAX
                function loadComentarios(idProduto) {
                    $.ajax({
                        type: 'GET',
                        url: 'carregar_comentarios.php',
                        data: {
                            id: idProduto
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.length > 0) {
                                var comentariosHtml = '<h2>Comentários</h2>';
                                response.forEach(function(comentario) {
                                    comentariosHtml += '<p><strong>' + comentario.nome + '</strong> em ' + comentario.data_comentario + ':<br>';
                                    comentariosHtml += comentario.comentario + '</p>';
                                });
                                $('#comentarios').html(comentariosHtml); // Atualiza a div de comentários
                            } else {
                                $('#comentarios').html('<p>Ainda não há comentários para este produto.</p>');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error(xhr.responseText); // Exibe a mensagem de erro no console
                            alert('Erro ao carregar os comentários'); // Exibe uma mensagem de erro genérica
                        }
                    });
                }

                function checkLogin() {
                    if (!<?php echo isset($_SESSION['login']) ? 'true' : 'false'; ?>) {
                        alert('Faça login para adicionar ao carrinho');

                        return false;
                    }
                    return true;
                }

                function redirectToLogin() {
                    window.location.href = '../../login.php';
                }

                // Carrega os comentários ao carregar a página
                $(document).ready(function() {
                    loadComentarios(<?php echo $row['id']; ?>);
                });

                function toggleDescription() {
                    const description = document.querySelector('.product-description');
                    const toggleLabel = document.querySelector('.toggle-label');

                    if (description.style.display === "none") {
                        description.style.display = "block";
                        toggleLabel.classList.remove('arrow-down');
                        toggleLabel.classList.add('arrow-up');
                    } else {
                        description.style.display = "none";
                        toggleLabel.classList.remove('arrow-up');
                        toggleLabel.classList.add('arrow-down');
                    }
                }
            </script>
            <script src="assets/js/script.js"></script>
        </body>

        </html>
<?php
    } else {
        echo "Produto não encontrado.";
    }
} else {
    echo "Slug de produto inválido.";
}

$stmt->close();
$conn->close();
?>