<?php
session_start();
include '../../conexao.php'; // Assumindo que este arquivo inclui a conexão com o banco de dados

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $cep = $_POST['cep_edit'];
    $cidade = $_POST['cidade_edit'];
    $estado = $_POST['estado_edit'];
    $rua = $_POST['rua_edit'];
    $bairro = $_POST['bairro_edit'];
    $numero = $_POST['numero_edit'];

    // Atualiza os dados do endereço no banco de dados
    $stmt = $conn->prepare("UPDATE enderecos SET cep = ?, cidade = ?, estado = ?, rua = ?, bairro = ?, numero = ? WHERE id = ?");
    $stmt->bind_param("sssssii", $cep, $cidade, $estado, $rua, $bairro, $numero, $id);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }
} else {
    echo "Método não permitido.";
}
?>