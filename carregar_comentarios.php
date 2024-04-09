<?php
session_start();
include '../../conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];

    
    $sql = "SELECT * FROM comentarios WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    
    while ($row = $result->fetch_assoc()) {
        echo "<p><strong>Autor:</strong> " . htmlspecialchars($row['author']) . "</p>";
        echo "<p><strong>Comentário:</strong> " . htmlspecialchars($row['comment']) . "</p>";
        echo "<hr>";
    }

    $stmt->close();
} else {
    
    http_response_code(400);
}
?>