<?php

session_start();

if (!isset($_SESSION['login']) || $_SESSION['tipo_usuario'] != 1) {
    header('Location: ../../index.php');
    exit();
}
include '../models/Produto.php';
include '../../conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = htmlspecialchars($_POST["nome"]);
    $descricao = htmlspecialchars($_POST["descricao"]);
    $preco = floatval($_POST["preco"]);
    $categoria = htmlspecialchars($_POST["categoria"]);

    if (isset($_FILES["imagem"]) && $_FILES["imagem"]["error"] == 0) {
        $imagem = file_get_contents($_FILES["imagem"]["tmp_name"]);

        $allowed_types = array('image/jpeg', 'image/png', 'image/gif');
        if (!in_array($_FILES['imagem']['type'], $allowed_types)) {
            die("Tipo de arquivo não suportado.");
        }

        if ($_FILES['imagem']['size'] > 1048576) {
            die("Tamanho do arquivo excedido.");
        }
    } else {
        $imagem = null;
    }

    $produto = new Produto($nome, $descricao, $preco, $categoria, $imagem);

    $sql = "INSERT INTO produto (nome, descricao, preco, imagem, slug, categoria) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdsss", $produto->getNome(), $produto->getDescricao(), $produto->getPreco(), $produto->getImagem(), $produto->getSlug(), $produto->getCategoria());

    if ($stmt->execute()) {
        echo json_encode(array("success" => true));
    } else {
        echo json_encode(array("success" => false, "message" => "Erro ao cadastrar o produto: " . $stmt->error));
    }


    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Cadastro de Produto</title>
    <link rel="stylesheet" type="text/css" href="./assets/css/stylecadastrar.css">
</head>

<body>
    <div class="container">
        <h2>Cadastro de Produto</h2>
        <form id="formProduto" method="post" enctype="multipart/form-data" action="cadastrar_produto.php">
            <label for="nome">Nome:</label><br>
            <input type="text" id="nome" name="nome" required><br><br>

            <label for="descricao">Descrição:</label><br>
            <textarea id="descricao" name="descricao"></textarea><br><br>

            <label for="preco">Preço:</label><br>
            <input type="number" id="preco" name="preco" step="0.01" required><br><br>

            <label for="categoria">Categoria:</label><br>
            <select id="categoria" name="categoria" required>
                <option value="">Selecione uma categoria</option>
                <option value="Eletrônicos">Eletrônicos</option>
                <option value="Roupas">Roupas</option>
                <option value="Acessórios">Acessórios</option>
            </select><br><br>

            <label for="imagem" class="file-upload-btn">
                Upload Imagem
                <input type="file" id="imagem" name="imagem" accept="image/*" required onchange="previewImage(this)">
            </label><br><br>

            <img id="imagem-preview" src="" alt="Preview da Imagem" style="max-width: 100%; display: none;"><br><br>

            <input type="submit" value="Cadastrar">
        </form>
        <di style="color: greenyellow;" id="mensagem"></div>

        <a href="../../index.php">Voltar</a>
    </div>
    <script src="./assets/js/scriptcadastro.js"></script>


</body>

</html>