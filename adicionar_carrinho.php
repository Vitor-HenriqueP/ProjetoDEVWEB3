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
        echo "Produto adicionado ao carrinho com sucesso.";
    } else {
        echo "Erro ao adicionar o produto ao carrinho: " . $conn->error;
    }

    $stmt->close(); // Fechar a declaração preparada
} else {
    echo "ID do produto não especificado.";
}

$conn->close();
?>
