<?php
include 'conexao.php'; // Assumindo que este arquivo inclui a conexão com o banco de dados

session_start();

if (!isset($_SESSION['login'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $novoNome = $_POST['novo_nome'];

    // Verifica se o campo Novo Nome está preenchido
    if (!empty($novoNome)) {
        // Atualiza o nome no banco de dados
        $stmt = $conn->prepare("UPDATE usuarios SET nome = ? WHERE id = ?");
        $stmt->bind_param("si", $novoNome, $_SESSION['id']);
        $stmt->execute();

        // Atualiza $_SESSION['nome'] com o novo nome
        $_SESSION['nome'] = $novoNome;

        // Redirecionar para uma página de confirmação
        header('Location: confirmacao_redefinir.php');
        exit();
    } else {
        // Exibe uma mensagem de erro caso o novo nome esteja vazio
        $erro = "Por favor, preencha o campo Novo Nome.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir nome</title>
</head>

<body>
    <h1>Redefinir nome</h1>
    <?php if (isset($erro)) {
        echo "<p>$erro</p>";
    } ?>
    <form method="post" action="redefinir_nome.php">
        <label for="novo_nome">Novo Nome:</label><br>
        <input value="<?php echo htmlspecialchars($_SESSION['nome']); ?>" type="text" id="novo_nome" name="novo_nome" required><br><br>

        <input type="submit" value="Redefinir">

        <a href="index.php">Voltar para a tela de inicio</a>
    </form>
</body>

</html>
