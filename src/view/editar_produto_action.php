<?php
session_start();

// Verifica se o usuário está logado e se é do tipo 1 (administrador)
if (!isset($_SESSION['login']) || $_SESSION['tipo_usuario'] != 1) {
    header('Location: ../../index.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
    $nome = $_POST["nome"];
    $descricao = $_POST["descricao"];
    $preco = floatval($_POST["preco"]);

    // Verifica se foi enviado um novo arquivo de imagem
    if (isset($_FILES["imagem"]) && $_FILES["imagem"]["error"] == 0 && getimagesize($_FILES["imagem"]["tmp_name"])) {
        $imagem = file_get_contents($_FILES["imagem"]["tmp_name"]);
    } else {
        // Se não foi enviado, mantém a imagem atual
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

    // Preparar a query SQL usando um prepared statement
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
