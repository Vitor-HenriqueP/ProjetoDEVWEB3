<?php
include './conexao.php'; // Inclua o arquivo de conexão
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_produto'])) {
    $id_produto = $_POST['id_produto'];
    $id_usuario = $_SESSION['id']; // Obter o ID do usuário da sessão

    $sql = "INSERT INTO carrinho (id_usuario, id_produto) VALUES ($id_usuario, $id_produto)";

    if ($conn->query($sql) === TRUE) {
        echo "Produto adicionado ao carrinho com sucesso.";
    } else {
        echo "Erro ao adicionar o produto ao carrinho: " . $conn->error;
    }
} else {
    echo "ID do produto não especificado.";
}

$conn->close();
?>
