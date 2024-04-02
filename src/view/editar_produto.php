<?php
session_start();

// Verifica se o usuário está logado e se é do tipo 1 (administrador)
if (!isset($_SESSION['login']) || $_SESSION['tipo_usuario'] != 1) {
    header('Location: ../../index.php');
    exit();
}

// Verifica se foi enviado o ID do produto a ser editado
if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    header('Location: ../../index.php');
    exit();
}

$id = $_POST['id'];

include '../../conexao.php'; // Inclua o arquivo de conexão

// Consulta SQL para obter os dados do produto com base no ID
$sql = "SELECT * FROM produto WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="./assets/css/stylecadastrar.css">
    <title>Editar Produto</title>
</head>

<body>
<div class="container">
    <h2>Editar Produto</h2>
    <form id="editForm" method="post" enctype="multipart/form-data" action="editar_produto_action.php">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <label for="nome">Nome:</label><br>
        <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($row['nome']); ?>" required><br><br>

        <label for="descricao">Descrição:</label><br>
        <textarea id="descricao" name="descricao"><?php echo htmlspecialchars($row['descricao']); ?></textarea><br><br>

        <label for="preco">Preço:</label><br>
        <input type="number" id="preco" name="preco" step="0.01" value="<?php echo $row['preco']; ?>" required><br><br>

        <label for="imagem" class="file-upload-btn">Imagem:</label><br>
        <input type="file" id="imagem" name="imagem" accept="image/*" onchange="previewImage(event)"><br>
        <img id="imagemAtual" src="data:image/jpeg;base64,<?php echo base64_encode($row['imagem']); ?>" alt="Imagem Atual" style="max-width: 100px;"><br><br>

        <input type="submit" value="Salvar">
    </form>
    <a href="../../index.php">Voltar para a página inicial</a>
    </div>
    <script>
        // Preencher os campos com os valores do produto
        var produto = {
            nome: "<?php echo htmlspecialchars($row['nome']); ?>",
            descricao: "<?php echo htmlspecialchars($row['descricao']); ?>",
            preco: "<?php echo $row['preco']; ?>",
            imagem: "<?php echo base64_encode($row['imagem']); ?>"
        };

        document.getElementById('nome').value = produto.nome;
        document.getElementById('descricao').value = produto.descricao;
        document.getElementById('preco').value = produto.preco;
        document.getElementById('imagemAtual').src = 'data:image/jpeg;base64,' + produto.imagem;

        function previewImage(event) {
            var input = event.target;
            var reader = new FileReader();
            reader.onload = function () {
                var imgElement = document.getElementById("imagemAtual");
                imgElement.src = reader.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    </script>
</body>

</html>

<?php
} else {
    echo "Produto não encontrado.";
}

$stmt->close();
$conn->close();
?>
