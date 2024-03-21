<?php
include 'conexao.php'; // Assumindo que este arquivo inclui a conexão com o banco de dados

session_start();

if (!isset($_SESSION['login'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $novaSenha = $_POST['nova_senha'];

    // Verifica se o campo Senha está preenchido
    if (!empty($novaSenha)) {
        // Você pode realizar validações dos dados recebidos aqui

        // Atualiza apenas a senha no banco de dados
        $stmt = $conn->prepare("UPDATE usuarios SET senha = ? WHERE id = ?");
        $stmt->bind_param("si", password_hash($novaSenha, PASSWORD_DEFAULT), $_SESSION['id']);
        $stmt->execute();

        // Redirecionar para uma página de confirmação
        header('Location: confirmacao_redefinir.php');
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
    <title>Redefinir senha</title>
</head>

<body>
    <h1>Redefinir senha</h1>
    <?php if (isset($erro)) {
        echo "<p>$erro</p>";
    } ?>
    <form method="post" action="redefinir_senha.php">
        <label for="nova_senha">Nova Senha:</label><br>
        <input type="password" id="nova_senha" name="nova_senha" required><br><br>

        <input type="submit" value="Redefinir">

        <a href="index.php">Voltar para a tela de inicio</a>
    </form>
</body>

</html>
