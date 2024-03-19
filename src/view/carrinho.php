<?php
include '../../conexao.php'; // Verifique o caminho do arquivo de conexão

session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['login'])) {
    header('Location: login.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrinho</title>
</head>
<body>
    <h1>Carrinho</h1>

    <?php
    // Consulta SQL para obter os produtos no carrinho do usuário logado
    $id_usuario = $_SESSION['id'];
    $sql = "SELECT p.nome, p.descricao, p.preco FROM carrinho c INNER JOIN produto p ON c.id_produto = p.id WHERE c.id_usuario = $id_usuario";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $total = 0; // Variável para armazenar o total dos produtos no carrinho

        while ($row = $result->fetch_assoc()) {
            $total += $row['preco']; // Adiciona o preço do produto ao total
            echo "<p>{$row['nome']} - {$row['descricao']} - R$ {$row['preco']}</p>";
        }

        // Exibe o valor total
        echo "<p><strong>Total: R$ $total</strong></p>";

        // Botão Comprar
        echo "<form method='post'>";
        echo "<input type='submit' name='comprar' value='Comprar'>";
        echo "</form>";

        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['comprar'])) {
            // Limpa todos os itens do carrinho no banco de dados
            $sql_delete = "DELETE FROM carrinho WHERE id_usuario = $id_usuario";
            if ($conn->query($sql_delete) === TRUE) {
                echo "<div id='compra-realizada' style='background-color: #dff0d8; color: #3c763d; padding: 10px; margin-top: 10px;'>Compra realizada!</div>";
                // Recarrega a página após 2 segundos
                echo "<script>setTimeout(function(){ location.reload(); }, 2000);</script>";
            } else {
                echo "Erro ao realizar a compra: " . $conn->error;
            }
        }
    } else {
        echo "<p>Carrinho vazio</p>";
    }
    ?>

    <a href="../../index.php">Voltar para a página inicial</a>
</body>
</html>

<?php
$conn->close();
?>
