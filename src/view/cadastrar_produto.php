<?php
session_start();

if (!isset($_SESSION['login']) || $_SESSION['tipo_usuario'] != 1) {
    header('Location: ../../index.php');
    exit();
}

include '../../conexao.php'; // Assumindo que este arquivo inclui a conexão com o banco de dados

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST["nome"];
    $descricao = $_POST["descricao"];
    $preco = floatval($_POST["preco"]);

    // Verifica se foi enviado um arquivo de imagem
    if (isset($_FILES["imagem"]) && $_FILES["imagem"]["error"] == 0) {
        $imagem = file_get_contents($_FILES["imagem"]["tmp_name"]);

        // Verifica o tipo de arquivo
        $allowed_types = array('image/jpeg', 'image/png', 'image/gif');
        if (!in_array($_FILES['imagem']['type'], $allowed_types)) {
            die("Tipo de arquivo não suportado.");
        }

        // Verifica o tamanho do arquivo (máximo de 1MB)
        if ($_FILES['imagem']['size'] > 1048576) {
            die("Tamanho do arquivo excedido.");
        }
    } else {
        $imagem = null;
    }

    // Preparar a query SQL usando um prepared statement
    $sql = "INSERT INTO produto (nome, descricao, preco, imagem) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssds", $nome, $descricao, $preco, $imagem);

    if ($stmt->execute()) {
        echo "Produto cadastrado com sucesso.";
    } else {
        echo "Erro ao cadastrar o produto: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Cadastro de Produto</title>
</head>

<body>
    <h2>Cadastro de Produto</h2>
    <form method="post" enctype="multipart/form-data">
        <label for="nome">Nome:</label><br>
        <input type="text" id="nome" name="nome" required><br><br>

        <label for="descricao">Descrição:</label><br>
        <textarea id="descricao" name="descricao"></textarea><br><br>

        <label for="preco">Preço:</label><br>
        <input type="number" id="preco" name="preco" step="0.01" required><br><br>

        <label for="imagem">Imagem:</label><br>
        <input type="file" id="imagem" name="imagem" accept="image/*" required><br><br>

        <input type="submit" value="Cadastrar">
    </form>
    <a href="../../index.php">Voltar </a>
</body>

</html>
