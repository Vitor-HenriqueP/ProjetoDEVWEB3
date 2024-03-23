<?php
session_start();
include '../../conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['author']) && isset($_POST['comment']) && isset($_POST['product_id'])) {
    $author = $_POST['author'];
    $comment = $_POST['comment'];
    $product_id = $_POST['product_id'];

    // Insira o comentário no banco de dados
    $sql = "INSERT INTO comentarios (author, comment, product_id) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssi', $author, $comment, $product_id);
    $stmt->execute();
    $stmt->close();

    // Responda com sucesso
    http_response_code(200);
} else {
    // Responda com erro
    http_response_code(400);
}
?>
