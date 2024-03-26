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
            <link rel="stylesheet" type="text/css" href="./assets/css/styleproduto.css">
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        </head>

        <body>
            <div class="container">
                <div class="product">
                    <div class="product-image">
                        <img src="data:image/jpeg;base64,<?php echo base64_encode($row['imagem']); ?>" alt="<?php echo htmlspecialchars($row['nome']); ?>">
                    </div>
                    <div class="product-info">
                        <h1 class="product-title"><?php echo htmlspecialchars($row['nome']); ?></h1>
                        <p class="product-description"><?php echo htmlspecialchars($row['descricao']); ?></p>
                        <p class="product-price">R$<?php echo number_format($row['preco'], 2, ',', '.'); ?></p>
                        <a href="../../index.php">Voltar para a página inicial</a>

                        <?php
                        // Verifica se o usuário está logado antes de mostrar os botões de editar e excluir
                        if (isset($_SESSION['login'])) {
                            // Formulário para adicionar ao carrinho
                            if ($_SESSION['tipo_usuario'] == 2) {
                                echo "<form method='post' action='../../adicionar_carrinho.php' onsubmit='return checkLogin()'>";
                                echo "<input type='hidden' name='id_produto' value='{$row['id']}'>";
                                echo "<input type='submit' value='Adicionar ao Carrinho'>";
                                echo "</form>";

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
                </div>
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
