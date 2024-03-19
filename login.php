<?php
include 'conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = $_POST['login'];
    $senha = $_POST['senha'];

    $sql = "SELECT * FROM usuarios WHERE login='$login'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        if (password_verify($senha, $row['senha'])) {
            // Login bem-sucedido
            session_start();
            $_SESSION['login'] = $login;
            header('Location: index.php');
            exit();
        } else {
            // Senha incorreta
            $erro = "Senha incorreta";
        }
    } else {
        // Usuário não encontrado
        $erro = "Usuário não encontrado";
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
