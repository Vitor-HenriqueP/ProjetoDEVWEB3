<?php
include 'conexao.php'; // Assumindo que este arquivo inclui a conexão com o banco de dados

session_start();

if (!isset($_SESSION['login'])) {
    header('Location: login.php');
    exit();
}

// Define o nome atual como padrão para o novo nome
$novoNome = $_SESSION['nome'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $novoNome = $_POST['novo_nome'];
    $novaSenha = $_POST['nova_senha'];

    // Verifica se o campo Senha está preenchido
    if (!empty($novaSenha)) {
        // Você pode realizar validações dos dados recebidos aqui

        // Se o campo Nome também estiver preenchido, atualiza o nome e a senha
        if (!empty($novoNome)) {
            $stmt = $conn->prepare("UPDATE usuarios SET nome = ?, senha = ? WHERE id = ?");
            $stmt->bind_param("ssi", $novoNome, password_hash($novaSenha, PASSWORD_DEFAULT), $_SESSION['id']);
            $stmt->execute();

            // Atualiza $_SESSION['nome'] com o novo nome
            $_SESSION['nome'] = $novoNome;
        } else {
            // Caso contrário, atualiza apenas a senha
            $stmt = $conn->prepare("UPDATE usuarios SET senha = ? WHERE id = ?");
            $stmt->bind_param("si", password_hash($novaSenha, PASSWORD_DEFAULT), $_SESSION['id']);
            $stmt->execute();
        }

        // Redirecionar para uma página de confirmação
        header('Location: confirmacao_redefinir_login.php');
        exit();
    } else {
        // Exibe uma mensagem de erro caso a senha esteja vazia
        $erro = "Por favor, preencha o campo Nova Senha.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir dados</title>
</head>

<body>
    <h1>Redefinir dados</h1>
    <?php if (isset($erro)) {
        echo "<p>$erro</p>";
    } ?>
    <form method="post" action="redefinir_login.php">
        <label for="novo_nome">Novo Nome:</label><br>
        <input type="text" id="novo_nome" name="novo_nome" value="<?php echo $novoNome; ?>"><br><br>

        <label for="nova_senha">Nova Senha:</label><br>
        <input type="password" id="nova_senha" name="nova_senha" required><br><br>

        <input type="submit" value="Redefinir">
    </form>
</body>

</html>
