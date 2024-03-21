<?php
session_start();

// Verifica se o usuário está logado e se é do tipo 1 (administrador)
if (!isset($_SESSION['login']) || $_SESSION['tipo_usuario'] != 1) {
    header('Location: ../../index.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"])) {
    $host = "localhost"; // host do banco de dados
    $username = "root"; // nome de usuário do banco de dados
    $password = ""; // senha do banco de dados
    $dbname = "ProjetoDEVWEB3"; // nome do banco de dados

    // Conexão com o banco de dados
    $conn = new mysqli($host, $username, $password, $dbname);

    // Verifica se houve algum erro na conexão
    if ($conn->connect_error) {
        die("Erro ao conectar ao banco de dados: " . $conn->connect_error);
    }

    $id = intval($_POST["id"]);

    // Preparar a query SQL usando um prepared statement
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
