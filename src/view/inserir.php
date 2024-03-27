<?php
session_start();

header('Content-Type: application/json');

// Verifica se a requisição é do tipo POST e se o usuário está logado
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['id'])) {
    // Limpa e valida os dados de entrada
    $comentario = filter_input(INPUT_POST, 'comentario', FILTER_SANITIZE_STRING);
    $id_usuario = $_SESSION['id'];
    $id_produto = filter_input(INPUT_POST, 'id_produto', FILTER_VALIDATE_INT);

    if ($id_produto === false) {
        echo json_encode('ID do produto inválido');
        exit;
    }

    // Conexão com o banco de dados
    include '../../conexao.php';

    try {
        // Declaração preparada para inserção de comentário
        $stmt = $conn->prepare('INSERT INTO comentario (id_usuario, id_produto, comentario) VALUES (?, ?, ?)');
        $stmt->bind_param('iis', $id_usuario, $id_produto, $comentario);

        if ($stmt->execute()) {
            echo json_encode('Comentário Salvo com Sucesso');
        } else {
            echo json_encode('Falha ao salvar comentário');
        }
    } catch (Exception $e) {
        echo json_encode('Erro ao inserir comentário: ' . $e->getMessage());
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode('Você precisa estar logado para comentar.');
}
?>