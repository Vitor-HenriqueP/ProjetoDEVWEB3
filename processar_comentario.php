<?php
session_start();
include '../../conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['author']) && isset($_POST['comment']) && isset($_POST['product_id'])) {
    // Validar e limpar os dados recebidos
    $author = filter_var($_POST['author'], FILTER_SANITIZE_STRING);
    $comment = filter_var($_POST['comment'], FILTER_SANITIZE_STRING);
    $product_id = filter_var($_POST['product_id'], FILTER_VALIDATE_INT);

    if (!$author || !$comment || !$product_id) {
        // Responda com erro se os dados não forem válidos
        http_response_code(400);
        exit();
    }

    // Adicionar token CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        // Responda com erro se o token CSRF estiver ausente ou inválido
        http_response_code(403);
        exit();
    }

    // Preparar a declaração SQL
    $sql = "INSERT INTO comentarios (author, comment, product_id) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssi', $author, $comment, $product_id);
    
    // Executar a inserção no banco de dados
    if ($stmt->execute()) {
        // Responder com sucesso
        http_response_code(200);
    } else {
        // Responder com erro em caso de falha na inserção
        http_response_code(500);
    }

    // Fechar a declaração e a conexão com o banco de dados
    $stmt->close();
    $conn->close();
} else {
    // Responder com erro se os dados necessários não forem recebidos
    http_response_code(400);
}
?>
