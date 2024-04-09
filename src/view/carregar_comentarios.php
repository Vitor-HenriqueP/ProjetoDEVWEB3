<?php
include '../../conexao.php'; 

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_produto = $_GET['id'];

    $comentarios_sql = "SELECT c.comentario, c.data_comentario, u.nome FROM comentario c JOIN usuarios u ON c.id_usuario = u.id WHERE id_produto = ?";
    $stmt_comentarios = $conn->prepare($comentarios_sql);
    $stmt_comentarios->bind_param('i', $id_produto);
    $stmt_comentarios->execute();
    $result_comentarios = $stmt_comentarios->get_result();

    $comentarios = array();
    while ($comentario_row = $result_comentarios->fetch_assoc()) {
        $comentarios[] = array(
            'nome' => htmlspecialchars($comentario_row['nome']),
            'data_comentario' => htmlspecialchars($comentario_row['data_comentario']),
            'comentario' => htmlspecialchars($comentario_row['comentario'])
        );
    }

    echo json_encode($comentarios);
} else {
    echo json_encode(array('error' => 'ID do produto não especificado ou inválido.'));
}

$stmt_comentarios->close();
$conn->close();
