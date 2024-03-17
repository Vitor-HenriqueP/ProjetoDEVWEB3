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

    <?php
    require'../../validador_acesso.php';
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

        $nome = $_POST["nome"];
        $descricao = $_POST["descricao"];
        $preco = $_POST["preco"];
        $imagem = addslashes(file_get_contents($_FILES["imagem"]["tmp_name"]));

        $sql = "INSERT INTO produto (nome, descricao, preco, imagem) VALUES ('$nome', '$descricao', $preco, '$imagem')";

        if ($conn->query($sql) === TRUE) {
            echo "Produto cadastrado com sucesso.";
        } else {
            echo "Erro ao cadastrar o produto: " . $conn->error;
        }

        $conn->close();
    }
    ?>
</body>
</html>
