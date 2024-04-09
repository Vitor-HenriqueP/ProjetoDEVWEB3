<?php
session_start();


if (!isset($_SESSION['login']) || $_SESSION['tipo_usuario'] != 1) {
    header('Location: ../../index.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $host = "localhost"; 
    $username = "root";
    $password = "";
    $dbname = "ProjetoDEVWEB3";

    $conn = new mysqli($host, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Erro ao conectar ao banco de dados: " . $conn->connect_error);
    }

    $id = intval($_POST["id"]);
    $nome = $_POST["nome"];
    $descricao = $_POST["descricao"];
    $preco = floatval($_POST["preco"]);
    if (isset($_FILES["imagem"]) && $_FILES["imagem"]["error"] == 0 && getimagesize($_FILES["imagem"]["tmp_name"])) {
        $imagem = file_get_contents($_FILES["imagem"]["tmp_name"]);
    } else {
        $sql_select_imagem = "SELECT imagem FROM produto WHERE id = ?";
        $stmt_select_imagem = $conn->prepare($sql_select_imagem);
        $stmt_select_imagem->bind_param("i", $id);
        $stmt_select_imagem->execute();
        $result_select_imagem = $stmt_select_imagem->get_result();

        if ($result_select_imagem->num_rows > 0) {
            $row_select_imagem = $result_select_imagem->fetch_assoc();
            $imagem = $row_select_imagem["imagem"];
        } else {
            $imagem = null;
        }

        $stmt_select_imagem->close();
    }
    $sql = "UPDATE produto SET nome = ?, descricao = ?, preco = ?, imagem = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdsi", $nome, $descricao, $preco, $imagem, $id);

    if ($stmt->execute()) {
        header('Location: ../../index.php');
        exit();
    } else {
        echo "Erro ao atualizar o produto: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>