<?php
session_start();


if (!isset($_SESSION['login']) || $_SESSION['tipo_usuario'] != 1) {
    header('Location: ../../index.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"])) {
    $host = "localhost";
    $username = "root";
    $password = "";
    $dbname = "ProjetoDEVWEB3";
    $conn = new mysqli($host, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Erro ao conectar ao banco de dados: " . $conn->connect_error);
    }

    $id = intval($_POST["id"]);
    $sql = "DELETE FROM produto WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header('Location: ../../index.php');
    } else {
        echo "Erro ao excluir o produto: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>