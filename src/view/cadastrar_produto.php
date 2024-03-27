<?php
session_start();

if (!isset($_SESSION['login']) || $_SESSION['tipo_usuario'] != 1) {
    header('Location: ../../index.php');
    exit();
}

include '../../conexao.php';

function slugify($text)
{
    $text = preg_replace('/[^\pL\d]+/u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = strtolower($text);
    $text = preg_replace('/[^-\w]+/', '', $text);
    $text = trim($text, '-');

    $randomString = substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(150 / strlen($x)))), 1, 150);
    $text = $text . '-' . $randomString;

    return $text;
}

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

    $slug = slugify($nome);

    $sql = "INSERT INTO produto (nome, descricao, preco, imagem, slug, categoria) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdsss", $nome, $descricao, $preco, $imagem, $slug, $categoria);

    if ($stmt->execute()) {
        header('Location: cadastrar_produto.php');
        exit();
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
    <style>
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
            display: none;
        }
    </style>
    <script>
        function showSuccessMessage() {
            document.getElementById("successMessage").style.display = "block";
            document.getElementById("formProduto").reset();
            setTimeout(function() {
                document.getElementById("successMessage").style.display = "none";
            }, 2000);
        }

        function submitForm() {
            var form = document.getElementById("formProduto");
            var formData = new FormData(form);

            var xhr = new XMLHttpRequest();
            xhr.open("POST", "", true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                    showSuccessMessage();
                }
            };
            xhr.send(formData);

            return false;
        }
    </script>
</head>

<body>
    <h2>Cadastro de Produto</h2>
    <div id="successMessage" class="success-message">Produto cadastrado com sucesso!</div>
    <form id="formProduto" method="post" enctype="multipart/form-data" onsubmit="return submitForm()">
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

        <label for="imagem">Imagem:</label><br>
        <input type="file" id="imagem" name="imagem" accept="image/*" required><br><br>

        <input type="submit" value="Cadastrar">
    </form>
    <a href="../../index.php">Voltar</a>
</body>

</html>
