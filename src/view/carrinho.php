<?php
session_start();

include '../../conexao.php'; // Inclua o arquivo de conexão com o banco de dados

// Função para criar um slug a partir de um texto
function slugify($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text); // Substitui caracteres não alfanuméricos por '-'
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text); // Converte caracteres especiais para equivalentes em ASCII
    $text = strtolower($text); // Converte para minúsculas
    $text = preg_replace('~[^-\w]+~', '', $text); // Remove caracteres que não são letras, números ou '-'
    $text = trim($text, '-'); // Remove '-' do início e fim do texto
    $text = preg_replace('~-+~', '-', $text); // Remove múltiplos '-' consecutivos
    return $text;
}

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
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_produto = filter_var($_POST['id_produto'], FILTER_VALIDATE_INT);
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    if ($id_produto === false || !in_array($action, ['add', 'remove'])) {
        exit('Ação inválida');
    }

    $id_usuario = intval($_SESSION['id']); // Obter o ID do usuário da sessão

    if ($action === 'remove') {
        $stmt = $conn->prepare("DELETE FROM carrinho WHERE id_usuario = ? AND id_produto = ? LIMIT 1");
        $stmt->bind_param("ii", $id_usuario, $id_produto);
        $stmt->execute();

        header("Location: $_SERVER[PHP_SELF]");
        exit();
    } elseif ($action === 'add') {
        $stmt = $conn->prepare("INSERT INTO carrinho (id_usuario, id_produto) VALUES (?, ?)");
        $stmt->bind_param("ii", $id_usuario, $id_produto);
        $stmt->execute();

        $mensagem = "Produto adicionado ao carrinho.";

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
            $total += $row['preco'] * $row['quantidade']; // Adiciona o preço do produto ao
            // Adiciona o preço do produto ao total
            $descricao = explode(' ', $row["descricao"]);
            $descricao = array_slice($descricao, 0, 20);
            $descricao = implode(' ', $descricao);
            
            echo "<a href='../../src/view/produto.php?slug=" . slugify($row['nome']) . "'>";
echo "<img src='data:image/jpeg;base64," . base64_encode($row['imagem']) . "' alt='" . $row['nome'] . "' class='product-image'>";
echo "</a>";

            echo "<form method='post' action='../../src/view/produto.php'>";
            echo "<input type='hidden' name='slug' value='" . slugify($row['nome']) . "'>";
            echo "<button type='submit' style='background: none; border: none; padding: 0; margin: 0;' class='product-image-button'>";
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
                echo '<meta http-equiv="refresh" content="2">';
            } else {
                echo "Erro ao finalizar a compra.";
            }
        }
    } else {
        echo "<p>Carrinho vazio</p>";
    }

    $stmt->close();
    ?>

</body>

</html>
