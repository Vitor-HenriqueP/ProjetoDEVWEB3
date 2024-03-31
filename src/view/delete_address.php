<?php
session_start();
include '../../conexao.php'; // Assumindo que este arquivo inclui a conexão com o banco de dados

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];

    $stmt = $conn->prepare("DELETE FROM enderecos WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }
}
?>
