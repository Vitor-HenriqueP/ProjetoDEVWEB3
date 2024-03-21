<?php
include 'conexao.php'; // Assumindo que este arquivo inclui a conexão com o banco de dados
include 'src/models/User.php'; // Supondo que o arquivo com a classe Usuario esteja nesse caminho

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = $_POST['login'];
    $senha = $_POST['senha'];

    $stmt = $conn->prepare("SELECT id, login, senha, tipo_usuario FROM usuarios WHERE login = ?");
    $stmt->bind_param("s", $login);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $usuarioEncontrado = $result->fetch_assoc();
        if (password_verify($senha, $usuarioEncontrado['senha'])) {
            // Login bem-sucedido
            $_SESSION['id'] = $usuarioEncontrado['id'];
            $_SESSION['login'] = $login;
            $_SESSION['tipo_usuario'] = $usuarioEncontrado['tipo_usuario'];
            header('Location: index.php');
            exit();
        } else {
            $erro = "Login ou senha incorretos";
        }
    } else {
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
    <?php if (isset($erro)) {
        echo "<p>$erro</p>";
    } ?>
    <form method="post" action="login.php">
        <label for="login">Login:</label><br>
        <input type="text" id="login" name="login" required><br><br>

        <label for="senha">Senha:</label><br>
        <input type="password" id="senha" name="senha" required><br><br>

        <input type="submit" value="Entrar">
        <a href="index.php">Voltar para a pagina inicial</a>
        <br>

        <a href="cadastro.php">Cadastre-se</a>

    </form>
</body>

</html>