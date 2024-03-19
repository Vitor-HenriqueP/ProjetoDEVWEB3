<?php
include '../../conexao.php'; // ajuste o caminho conforme necessário

session_start();

if (!isset($_SESSION['id'])) {
    echo "Faça login para acessar o carrinho.";
    exit();
}

$id_usuario = $_SESSION['id'];

$sql = "SELECT produto.id, produto.nome, produto.descricao, produto.preco FROM carrinho JOIN produto ON carrinho.id_produto = produto.id WHERE carrinho.id_usuario = $id_usuario";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "ID: " . $row["id"] . " - Nome: " . $row["nome"] . " - Descrição: " . $row["descricao"] . " - Preço: R$" . number_format($row["preco"], 2, ',', '.') . "<br>";
    }
} else {
    echo "Nenhum produto no carrinho.";
}

$conn->close();
?>
