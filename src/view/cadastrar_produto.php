<?php

session_start();

class Produto {
    private $nome;
    private $descricao;
    private $preco;
    private $categoria;
    private $imagem;
    private $slug;

    public function __construct($nome, $descricao, $preco, $categoria, $imagem = null) {
        $this->nome = $nome;
        $this->descricao = $descricao;
        $this->preco = $preco;
        $this->categoria = $categoria;
        $this->imagem = $imagem;
        $this->slug = $this->slugify($nome);
    }

    public function getNome() {
        return $this->nome;
    }

    public function getDescricao() {
        return $this->descricao;
    }

    public function getPreco() {
        return $this->preco;
    }

    public function getCategoria() {
        return $this->categoria;
    }

    public function getImagem() {
        return $this->imagem;
    }

    public function getSlug() {
        return $this->slug;
    }

    private function slugify($text) {
        $text = preg_replace('/[^\pL\d]+/u', '-', $text);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = strtolower($text);
        $text = preg_replace('/[^-\w]+/', '', $text);
        $text = trim($text, '-');

        $randomString = substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(150 / strlen($x)))), 1, 150);
        $text = $text . '-' . $randomString;

        return $text;
    }
}

if (!isset($_SESSION['login']) || $_SESSION['tipo_usuario'] != 1) {
    header('Location: ../../index.php');
    exit();
}

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
    <link rel="stylesheet" type="text/css" href="./assets/css/stylecadastrar.css">
</head>

<body>
    <div class="container">
        <h2>Cadastro de Produto</h2>
        <div id="successMessage" class="success-message">Produto cadastrado com sucesso!</div>
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
        <a href="../../index.php">Voltar</a>
    </div>
    <script src="./assets/js/scriptcadastro.js"></script>
</body>

</html>
