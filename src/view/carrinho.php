<?php
include '../../conexao.php'; // Verifique o caminho do arquivo de conexão

session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['login'])) {
    header('Location: ../../login.php');
    exit();
}

// Verifica se o ID do produto foi enviado por POST
if (isset($_POST['id_produto']) && isset($_POST['action'])) {
    $id_produto = $_POST['id_produto'];
    $id_usuario = $_SESSION['id']; // Obter o ID do usuário da sessão

    if ($_POST['action'] === 'remove') {
        // Remove um item do carrinho
        $sql = "DELETE FROM carrinho WHERE id_usuario = $id_usuario AND id_produto = $id_produto LIMIT 1";
    } elseif ($_POST['action'] === 'add') {
        // Adiciona um item ao carrinho
        $sql = "INSERT INTO carrinho (id_usuario, id_produto) VALUES ($id_usuario, $id_produto)";
    }

    if ($conn->query($sql) === TRUE) {
        // Atualiza a página após a alteração no carrinho
        header('Location: carrinho.php');
        exit();
    } else {
        echo "Erro ao atualizar o carrinho: " . $conn->error;
    }
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
    $sql = "SELECT c.id_produto, COUNT(c.id_produto) as quantidade, p.nome, p.descricao, p.preco, p.imagem FROM carrinho c INNER JOIN produto p ON c.id_produto = p.id WHERE c.id_usuario = $id_usuario GROUP BY c.id_produto";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $total = 0; // Variável para armazenar o total dos produtos no carrinho

        while ($row = $result->fetch_assoc()) {
            $total += $row['preco'] * $row['quantidade']; // Adiciona o preço do produto ao total
            echo "<p>{$row['nome']} - {$row['descricao']} - R$ {$row['preco']} - Quantidade: {$row['quantidade']}</p>";
            echo "<img width = 100px src='data:image/jpeg;base64," . base64_encode($row['imagem']) . "' alt='{$row['nome']}'>";
            echo "<form method='post'>";
            echo "<input type='hidden' name='id_produto' value='{$row['id_produto']}'>";
            echo "<input type='hidden' name='action' value='remove'>";
            echo "<input type='submit' value='-'>";
            echo "</form>";

            echo "<form method='post'>";
            echo "<input type='hidden' name='id_produto' value='{$row['id_produto']}'>";
            echo "<input type='hidden' name='action' value='add'>";
            echo "<input type='submit' value='+'>";
            echo "</form>";
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
