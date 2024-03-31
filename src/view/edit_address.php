<?php
session_start();
include '../../conexao.php'; // Assumindo que este arquivo inclui a conexão com o banco de dados

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recebe o ID do endereço a ser editado
    $id = $_POST['id'];

    // Consulta o endereço no banco de dados
    $stmt = $conn->prepare("SELECT * FROM enderecos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Retorna os dados do endereço como JSON
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode($row);
    } else {
        echo "error";
    }
}
?>
