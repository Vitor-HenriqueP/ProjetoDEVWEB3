<?php
session_start();

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

// Verifica se o parâmetro 'id' foi passado na URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Consulta SQL para obter os detalhes do produto com base no ID
    $sql = "SELECT * FROM produto WHERE id = $id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        ?>
        <!DOCTYPE html>
        <html lang="pt-br">
        <head>
            <meta charset="UTF-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?php echo $row['nome']; ?></title>
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
            </style>
        </head>
        <body>
        <div class="container">
            <div class="product">
                <div class="product-image">
                    <img src="data:image/jpeg;base64,<?php echo base64_encode($row['imagem']); ?>" alt="<?php echo $row['nome']; ?>">
                </div>
                <div class="product-info">
                    <h1 class="product-title"><?php echo $row['nome']; ?></h1>
                    <p class="product-description"><?php echo $row['descricao']; ?></p>
                    <p class="product-price">R$<?php echo number_format($row['preco'], 2, ',', '.'); ?></p>
                    <?php
                    // Verifica se o usuário está logado
                    if (isset($_SESSION['login'])) {
                        echo "<form method='post' action='../../adicionar_carrinho.php'>";
                        echo "<input type='hidden' name='id_produto' value='$id'>";
                        echo "<input type='submit' value='Adicionar ao Carrinho'>";
                        echo "</form>";
                    } else {
                        echo "<p>Faça login para adicionar este produto ao carrinho.</p>";
                    }
                    ?>
                </div>
            </div>
        </div>
        </body>
        </html>
        <?php
    } else {
        echo "Produto não encontrado.";
    }
} else {
    echo "ID do produto não especificado.";
}

$conn->close();
?>
