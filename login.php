<?php
include 'conexao.php';
include 'src/models/User.php'; // Supondo que o arquivo com a classe Usuario esteja nesse caminho

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = $_POST['login'];
    $senha = $_POST['senha'];

    $usuario = new Usuario($conn); // Criando uma instância da classe Usuario
    $usuarioEncontrado = $usuario->findByLogin($login); // Buscando o usuário pelo login

    if ($usuarioEncontrado && password_verify($senha, $usuarioEncontrado['senha'])) {
        // Login bem-sucedido
        $_SESSION['id'] = $usuarioEncontrado['id']; // Definindo o ID do usuário na sessão
        $_SESSION['login'] = $login;
        $_SESSION['tipo_usuario'] = $usuarioEncontrado['tipo_usuario']; // Armazenando o tipo de usuário na sessão
        header('Location: index.php');
        exit();
    } else {
        // Senha incorreta ou usuário não encontrado
        $erro = "Login ou senha incorretos";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h1>Login</h1>
    <?php if(isset($erro)) { echo "<p>$erro</p>"; } ?>
    <form method="post" action="login.php">
        <label for="login">Login:</label><br>
        <input type="text" id="login" name="login" required><br><br>

        <label for="senha">Senha:</label><br>
        <input type="password" id="senha" name="senha" required><br><br>

        <input type="submit" value="Entrar">
    </form>
</body>
</html>