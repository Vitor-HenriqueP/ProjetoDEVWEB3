<?php
session_start();

include '../../conexao.php'; // Inclua o arquivo de conexão

// Verifica se o ID do produto foi enviado via POST e é um número inteiro válido
if (isset($_POST['id']) && is_numeric($_POST['id'])) {
    $id = $_POST['id'];

    // Consulta SQL para obter os detalhes do produto com base no ID
    $sql = "SELECT * FROM produto WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
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
                        <a href="../../index.php">Voltar para a pagina inicial</a>

                        <?php
                        // Verifica se o usuário está logado antes de mostrar os botões de editar e excluir
                        if (isset($_SESSION['login']) ) {
                            // Formulário para adicionar ao carrinho
                            if (isset($_SESSION['login']) && ($_SESSION['tipo_usuario'] !=1)) {

                            echo "<form method='post' action='../../adicionar_carrinho.php' onsubmit='return checkLogin()'>";
                            echo "<input type='hidden' name='id_produto' value='$id'>";
                            echo "<input type='submit' value='Adicionar ao Carrinho'>";
                            echo "</form>";
                            }
                            // Botões de editar e excluir (para usuários do tipo 1)
                            if ($_SESSION['tipo_usuario'] == 1) {
                                echo "<form method='post' action='editar_produto.php'>";
                                echo "<input type='hidden' name='id' value='$id'>";
                                echo "<input type='submit' value='Editar'>";
                                echo "</form>";

                                echo "<form method='post' action='excluir_produto.php'>";
                                echo "<input type='hidden' name='id' value='$id'>";
                                echo "<input type='submit' value='Excluir' onclick='return confirm(\"Tem certeza que deseja excluir este produto?\")'>";
                                echo "</form>";
                            }
                        } else {
                            // Mostra mensagem e redireciona para a página de login
                            echo "<p>Faça login para adicionar ao carrinho</p>";
                            echo "<button onclick='redirectToLogin()'>Login</button>";
                        }
                        ?>
                    </div>
                </div>
            </div>
            <section class="content">
		        <div class="box_form">
			        <h1>Deixe seu Comentário:</h1>
			        <form id="form1">
				        <label for="name">Nome</label><br>
				        <input type="text" name="name" id="name"/><br><br>

				        <label for="comment">Comentário</label><br>
				        <textarea name="comment" id="comment"></textarea><br><br>

				        <input type="submit" form="form1" class="btn-sub" value="Enviar Comentário"/><br><br>
			        </form>
		        </div>

		        <div class="box_comment">
		        </div>
	        </section>
            <script>
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
            </script>
            <script src="assets/js/jQuery/jquery-3.5.1.min.js"></script>
	        <script src="assets/js/script.js"></script>
        </body>

        </html>
<?php
    } else {
        echo "Produto não encontrado.";
    }
} else {
    echo "ID do produto não especificado ou inválido.";
}

$stmt->close();
$conn->close();
?>
