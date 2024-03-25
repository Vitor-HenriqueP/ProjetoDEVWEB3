<?php
include './conexao.php'; // Inclua o arquivo de conexão
session_start();

$mensagem = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_produto'])) {
    // Validar o ID do produto como um número inteiro
    $id_produto = filter_input(INPUT_POST, 'id_produto', FILTER_VALIDATE_INT);
    if ($id_produto === false || $id_produto === null) {
        $mensagem = "ID do produto inválido.";
    } else {
        // Obter o ID do usuário da sessão
        $id_usuario = $_SESSION['id'] ?? null;

        if ($id_usuario === null) {
            $mensagem = "Usuário não autenticado.";
        } else {
            // Preparar a consulta SQL usando prepared statements
            $stmt = $conn->prepare("INSERT INTO carrinho (id_usuario, id_produto) VALUES (?, ?)");
            $stmt->bind_param("ii", $id_usuario, $id_produto);

            if ($stmt->execute()) {
                $mensagem = "Produto adicionado ao carrinho.";
                // Redirecionar para o carrinho após 3 segundos
                echo '<meta http-equiv="refresh" content="3;url=index.php">';
            } else {
                $mensagem = "Erro ao adicionar o produto ao carrinho: " . $conn->error;
            }

            $stmt->close(); // Fechar a declaração preparada
        }
    }
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
    <div class="card-mensagem" id="mensagem"><?php echo htmlspecialchars($mensagem); ?></div>
</body>

</html>
