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
                    <a href="../../index.php"><img src="./assets/imagens/newlogo.png" alt="Logo Estação Digital" class="img_logo_header"></a>
                </div>
                <div class="navigation_header" id="navigation_header">
                    <button onclick="toggleSidebar()" class="btn_icon_header">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                            <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z" />
                        </svg>
                    </button>
                    <?php if (!isset($_SESSION['login'])) : ?>
                        <a href="../../login.php"><span class="material-symbols-outlined">login</span></a>
                    <?php endif; ?>
                    <?php if (!isset($_SESSION['login']) || ($_SESSION['tipo_usuario'] == 2)) : ?>
                        <a href="carrinho.php"><span class="material-symbols-outlined">shopping_cart</span></a>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['login'])) : ?>
                        <a href="../../user_config.php"><span class="material-symbols-outlined">person</span></a>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['login'])) : ?>
                        <form method="post" action="../../index.php">
                            <input type="hidden" name="logout" value="1">
                            <button type="submit"  style="background: none; border: none; cursor: pointer;">
                                <a><span class="material-symbols-outlined" style="vertical-align: middle;">logout</span></a>
                            </button>
                        </form>

                    <?php endif; ?>

                </div>
            </div>
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
                <label class="toggle-label" onclick="toggleDescription()">
                    <div class="description-toggle">
                        <a>
                            <span class="material-symbols-outlined description-icon">description</span>
                        </a>
                        <a>
                            <label class="description">Descrição</label>
                        </a>
                        <a>
                            <span class="material-symbols-outlined expand-more-icon">expand_more</span>
                        </a>
                        <a>
                            <span class="material-symbols-outlined expand-less-icon">expand_less</span>
                        </a>
                    </div>
                </label>
                <p class="product-description"><?php echo htmlspecialchars($row['descricao']); ?></p>
            </div>


            <div class="container">
        <?php
                                echo "<div id='comentarios' class='comments'></div>"; // Adicionando a classe 'hidden' para esconder os comentários inicialmente

                                // Formulário para adicionar comentário
                                echo "<form id='form1' method='post' action='inserir.php'>";
                                echo "<input type='hidden' name='id_produto' value='{$row['id']}'>";
                                echo "<div class= 'adicionarContainer'>";
                                echo "<a><h2 for='comment'>Adicionar comentário</h2></a><br>";
                                echo "<textarea name='comentario' id='comment' class='textAdd' required></textarea><br><br>";
                                echo "<input type='submit' name='enviar_comentario' value='Enviar Comentário' class='buttonAdd'>";
                                echo "</form>";
                                echo "</div>";
                            }
                            // Botões de editar e excluir (para usuários do tipo 1)
                            if ($_SESSION['tipo_usuario'] == 1) {
                                echo "<form method='post' action='editar_produto.php'>";
                                echo "<input type='hidden' name='id' value='{$row['id']}'>";
                                echo "<input type='submit' value='Editar'class='button'>";
                                echo "</form>";

                                echo "<form method='post' action='excluir_produto.php'>";
                                echo "<input type='hidden' name='id' value='{$row['id']}'>";
                                echo "<input type='submit' value='Excluir' class='button' onclick='return confirm(\"Tem certeza que deseja excluir este produto?\")'>";
                                echo "<div id='comentarios' class='commentsAdmin'></div>";
                                echo "</form>";
                            }
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
                                var comentariosHtml = '<h2><a>Comentários</a></h2>';
                                response.forEach(function(comentario) {
                                    comentariosHtml += '<br><p><strong>' + comentario.nome + '</strong> em ' + comentario.data_comentario + ':<br> <br>';
                                    comentariosHtml += 'Comentario: '+comentario.comentario + '</p>';
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

                document.addEventListener("DOMContentLoaded", function() {
                    var container = document.querySelector('.containerDesc');
                    var expandMoreIcon = container.querySelector('.expand-more-icon');
                    var expandLessIcon = container.querySelector('.expand-less-icon');
                    var productDescription = container.querySelector('.product-description');

                    expandMoreIcon.style.display = 'inline';
                    expandLessIcon.style.display = 'none';
                    productDescription.style.display = 'none';
                });

                function toggleDescription() {
                    var container = document.querySelector('.containerDesc');
                    var descriptionIcon = container.querySelector('.description-icon');
                    var expandMoreIcon = container.querySelector('.expand-more-icon');
                    var expandLessIcon = container.querySelector('.expand-less-icon');
                    var productDescription = container.querySelector('.product-description');

                    container.classList.toggle('expanded');
                    container.classList.toggle('collapsed');

                    if (container.classList.contains('expanded')) {
                        expandMoreIcon.style.display = 'none';
                        expandLessIcon.style.display = 'inline';
                        productDescription.style.display = 'block';
                    } else {
                        expandMoreIcon.style.display = 'inline';
                        expandLessIcon.style.display = 'none';
                        productDescription.style.display = 'none';
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