<?php
session_start();

$tipo_usuario = null;

if (isset($_SESSION['tipo_usuario'])) {
    $tipo_usuario = $_SESSION['tipo_usuario'];
}

if ($tipo_usuario != 1) {
    header('Location: ../../index.php'); // Redireciona para a página inicial se o usuário não for do tipo 1
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

    $nome = mysqli_real_escape_string($conn, $_POST["nome"]);
    $descricao = mysqli_real_escape_string($conn, $_POST["descricao"]);
    $preco = floatval($_POST["preco"]);

    // Verifica se foi enviado um arquivo de imagem
    if (isset($_FILES["imagem"]) && $_FILES["imagem"]["error"] == 0) {
        $imagem = file_get_contents($_FILES["imagem"]["tmp_name"]);
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
        echo "Erro ao cadastrar o produto: " . $conn->error;
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
        <input type="file" id="imagem" name="imagem" accept="image/*"><br><br>

        <input type="submit" value="Cadastrar">
    </form>
</body>
</html>
