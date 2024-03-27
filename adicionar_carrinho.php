<?php
include './conexao.php'; // Inclua o arquivo de conexão
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_produto'])) {
    $id_produto = $_POST['id_produto'];
    $id_usuario = $_SESSION['id']; // Obter o ID do usuário da sessão

    // Preparar a consulta SQL usando prepared statements
    $stmt = $conn->prepare("INSERT INTO carrinho (id_usuario, id_produto) VALUES (?, ?)");
    $stmt->bind_param("ii", $id_usuario, $id_produto);

    if ($stmt->execute()) {
        // Definir uma mensagem de sucesso para exibir
        $mensagem = "Produto adicionado ao carrinho.";
        // Redirecionar para o carrinho após 3 segundos
        echo '<script>window.setTimeout(function() { window.location.href = "src/view/carrinho.php"; }, 1000);</script>';
    } else {
        $mensagem = "Erro ao adicionar o produto ao carrinho: " . $conn->error;
    }

    $stmt->close(); // Fechar a declaração preparada
} else {
    $mensagem = "ID do produto não especificado.";
}

$conn->close();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Minha Loja</title>
    <style>
        .card-mensagem {
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px;
            margin: 20px auto;
            max-width: 400px;
            background-color: #f9f9f9;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: <?php echo isset($mensagem) ? 'block' : 'none'; ?>;
        }
    </style>
</head>

<body>
    <div class="card-mensagem" id="mensagem"><?php echo $mensagem; ?></div>
</body>

</html>
