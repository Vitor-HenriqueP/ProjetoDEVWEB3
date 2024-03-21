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
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 0;
                    background-color: #f5f5f5;
                }

                .container {
                    max-width: 1200px;
                    margin: 0 auto;
                    padding: 20px;
                }

                .product {
                    display: flex;
                    align-items: flex-start;
                }

                .product-image {
                    flex: 1;
                    margin-right: 20px;
                }

                .product-image img {
                    max-width: 100%;
                    height: auto;
                }

                .product-info {
                    flex: 2;
                }

                .product-title {
                    font-size: 24px;
                    margin-bottom: 10px;
                }

                .product-description {
                    margin-bottom: 20px;
                }

                .product-price {
                    font-size: 28px;
                    font-weight: bold;
                    margin-bottom: 20px;
                }

                form {
                    margin-top: 20px;
                }

                form input[type="submit"] {
                    padding: 10px 20px;
                    background-color: #007bff;
                    color: white;
                    border: none;
                    cursor: pointer;
                }

                form input[type="submit"]:hover {
                    background-color: #0056b3;
                }
            </style>
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
                        // Verifica se o usuário está logado antes de mostrar o botão de adicionar ao carrinho
                        if (isset($_SESSION['login'])) {
                            // Formulário para adicionar ao carrinho
                            echo "<form method='post' action='../../adicionar_carrinho.php' onsubmit='return checkLogin()'>";
                            echo "<input type='hidden' name='id_produto' value='$id'>";
                            echo "<input type='submit' value='Adicionar ao Carrinho'>";
                            echo "</form>";
                        } else {
                            // Mostra mensagem e redireciona para a página de login
                            echo "<p>Faça login para adicionar ao carrinho</p>";
                            echo "<button onclick='redirectToLogin()'>Login</button>";
                        }
                        ?>
                    </div>
                </div>
            </div>
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