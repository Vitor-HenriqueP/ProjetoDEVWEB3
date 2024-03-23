<?php
session_start(); // Inicie a sessão no início do arquivo

include '../../conexao.php'; // Inclua o arquivo de conexão com o banco de dados

// Verifique se o usuário está logado
if (!isset($_SESSION['login'])) {
    echo '<script>alert("Faça login para acessar o carrinho");</script>';
    echo '<script>setTimeout(function(){ window.location.href = "../../login.php"; });</script>';
    exit();
}

$tipo_usuario = isset($_SESSION['tipo_usuario']) ? $_SESSION['tipo_usuario'] : null;

if ($tipo_usuario !== null && $tipo_usuario != 2) {
    echo '<script>alert("Você não tem permissão para acessar esta página.");</script>';
    echo '<script>setTimeout(function(){ window.location.href = "../../index.php"; });</script>';
    exit();
}

// Verifique se o ID do produto foi enviado por POST
if (isset($_POST['id_produto']) && isset($_POST['action'])) {
    $id_produto = intval($_POST['id_produto']);
    $id_usuario = intval($_SESSION['id']); // Obter o ID do usuário da sessão

    if ($_POST['action'] === 'remove') {
        // Remove um item do carrinho
        $stmt = $conn->prepare("DELETE FROM carrinho WHERE id_usuario = ? AND id_produto = ? LIMIT 1");
        $stmt->bind_param("ii", $id_usuario, $id_produto);
        $stmt->execute();

        // Redireciona o usuário para a mesma página usando GET
        header("Location: $_SERVER[PHP_SELF]");
        exit();
    } elseif ($_POST['action'] === 'add') {
        // Adiciona um item ao carrinho
        $stmt = $conn->prepare("INSERT INTO carrinho (id_usuario, id_produto) VALUES (?, ?)");
        $stmt->bind_param("ii", $id_usuario, $id_produto);
        $stmt->execute();

        // Definir uma mensagem de sucesso para exibir
        $mensagem = "Produto adicionado ao carrinho.";
        // Exibir a mensagem de produto adicionado ao carrinho por 3 segundos
        echo "<script>setTimeout(function() { document.getElementById('mensagem').style.display = 'none'; }, 3000);</script>";

        // Redireciona o usuário para a mesma página usando GET
        header("Location: $_SERVER[PHP_SELF]");
        exit();
    }
}

// Atualizar a quantidade de itens no carrinho na sessão
$stmt_quantidade = $conn->prepare("SELECT COUNT(id_produto) as quantidade FROM carrinho WHERE id_usuario = ?");
$stmt_quantidade->bind_param("i", $id_usuario);
$stmt_quantidade->execute();
$result_quantidade = $stmt_quantidade->get_result();
$row_quantidade = $result_quantidade->fetch_assoc();
$_SESSION['quantidade_carrinho'] = $row_quantidade['quantidade'];
$stmt_quantidade->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrinho</title>
    <link rel="stylesheet" type="text/css" href="/config/stylecarrinho.css">
    
</head>

<body>
    <h1>Carrinho</h1>

    <?php
    // Consulta SQL para obter os produtos no carrinho do usuário logado
    $id_usuario = intval($_SESSION['id']);
    $stmt = $conn->prepare("SELECT c.id_produto, COUNT(c.id_produto) as quantidade, p.nome, p.descricao, p.preco, p.imagem FROM carrinho c INNER JOIN produto p ON c.id_produto = p.id WHERE c.id_usuario = ? GROUP BY c.id_produto");
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $total = 0; // Variável para armazenar o total dos produtos no carrinho

        while ($row = $result->fetch_assoc()) {
            $total += $row['preco'] *
            $row['quantidade']; // Adiciona o preço do produto ao total
            $descricao = explode(' ', $row["descricao"]);
            $descricao = array_slice($descricao, 0, 20);
            $descricao = implode(' ', $descricao);

            echo "<form method='post' action='../../src/view/produto.php'>";
            echo "<input type='hidden' name='id' value='" . $row["id_produto"] . "'>";
            echo "<button type='submit' style='background: none; border: none; padding: 0; margin: 0;' class='product-image-button'>";
            echo "<img src='data:image/jpeg;base64," . base64_encode($row['imagem']) . "' alt='" . $row['nome'] . "' class='product-image'>";
            echo "</button>";
            echo "</form>";

            echo "<p>{$descricao} - R$ {$row['preco']} - Quantidade: {$row['quantidade']}</p>";

            echo "<form method='post'>";
            echo "<input type='hidden' name='id_produto' value='{$row['id_produto']}'>";
            echo "<input type='hidden' name='action' value='remove'>";
            echo "<button type='submit' class='button'>-</button>";
            echo "</form>";

            echo "<form method='post'>";
            echo "<input type='hidden' name='id_produto' value='{$row['id_produto']}'>";
            echo "<input type='hidden' name='action' value='add'>";
            echo "<button type='submit' class='button'>+</button>";
            echo "</form>";
        }

        // Exibe o valor total
        echo "<p><strong>Total: R$ $total</strong></p>";

        // Botão Comprar
        echo "<form method='post'>";
        echo "<input type='submit' name='comprar' value='Comprar' class='button'>";
        echo "</form>";

        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['comprar'])) {
            // Limpa todos os itens do carrinho no banco de dados
            $stmt_delete = $conn->prepare("DELETE FROM carrinho WHERE id_usuario = ?");
            $stmt_delete->bind_param("i", $id_usuario);
            if ($stmt_delete->execute()) {
                // Recarrega a página após 2 segundos
                echo "<script>setTimeout(function(){ location.reload(); }, 500);</script>";
                echo "<div id='compra-realizada' style='background-color: #dff0d8; color: #3c763d; padding: 10px; margin-top: 10px;'>Compra realizada!</div>";
            } else {
                echo "Erro ao realizar a compra: " . $conn->error;
            }
        }
    } else {
        echo "<p>Carrinho vazio</p>";
    }
    ?>

    <a href="../../index.php">Voltar para a página inicial</a>

    <div class="card-mensagem" id="mensagem"><?php echo isset($mensagem) ? $mensagem : ''; ?></div>
</body>

</html>

<?php
$stmt->close();
$conn->close();
?>
