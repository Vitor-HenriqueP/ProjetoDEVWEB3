<?php
include 'conexao.php'; // Assumindo que este arquivo inclui a conexão com o banco de dados

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = $_POST['login'];
    $senha = $_POST['senha'];

    $stmt = $conn->prepare("SELECT id, nome, login, senha, tipo_usuario FROM usuarios WHERE login = ?");
    $stmt->bind_param("s", $login);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $usuarioEncontrado = $result->fetch_assoc();
        if (password_verify($senha, $usuarioEncontrado['senha'])) {
            // Login bem-sucedido
            $_SESSION['id'] = $usuarioEncontrado['id'];
            $_SESSION['nome'] = $usuarioEncontrado['nome']; // Adicione esta linha para armazenar o nome do usuário na sessão
            $_SESSION['login'] = $login;
            $_SESSION['tipo_usuario'] = $usuarioEncontrado['tipo_usuario']; // Corrigido para 'tipo_user'
            echo 'success'; // Retorna 'success' se o login for bem-sucedido
            exit();
        } else {
            echo 'Login ou senha incorretos';
            exit();
        }
    } else {
        echo 'Login ou senha incorretos';
        exit();
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('form').submit(function(event) {
                event.preventDefault();
                var formData = $(this).serialize();
                $.ajax({
                    type: 'POST',
                    url: 'login.php',
                    data: formData,
                    success: function(response) {
                        if (response === 'success') {
                            window.location.href = 'index.php';
                        } else {
                            $('#error-message').text(response);
                        }
                    }
                });
            });
        });
    </script>
</head>

<body>
    <h1>Login</h1>
    <div id="error-message"></div>
    <form method="post">
        <label for="login">Login:</label><br>
        <input type="text" id="login" name="login" required><br><br>

        <label for="senha">Senha:</label><br>
        <input type="password" id="senha" name="senha" required><br><br>

        <input type="submit" value="Entrar">
        <a href="index.php">Voltar para a página inicial</a>
        <br>

        <a href="cadastro.php">Cadastre-se</a>
    </form>
</body>

</html>
