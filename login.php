<?php
include 'conexao.php'; // Assumindo que este arquivo inclui a conexão com o banco de dados
include 'src/models/User.php';

$usuario = new Usuario_Padrao($conn);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
    $login = filter_input(INPUT_POST, 'login', FILTER_SANITIZE_STRING);
    $senha = filter_input(INPUT_POST, 'senha', FILTER_SANITIZE_STRING);

    // Validação de campos
    if (empty($nome) || empty($login) || empty($senha)) {
    } else {
        // Verificar se o login já está em uso
        $stmt_verificar = $conn->prepare("SELECT id FROM usuarios WHERE login = ?");
        $stmt_verificar->bind_param("s", $login);
        $stmt_verificar->execute();
        $result_verificar = $stmt_verificar->get_result();

        if ($result_verificar->num_rows > 0) {

            $mensagem = "Login já está em uso.";
            echo  $mensagem;
            exit();
        } else {
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            if ($usuario->cadastrarUsuario($nome, $login, $senha_hash)) {
                // Cadastro bem-sucedido
             
            $mensagem2 = "Cadastro bem-sucedido";
            echo  $mensagem2;
                exit();
            } else {
                // Caso ocorra algum erro no cadastro
            }
        }
    }
}

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
            echo 'sucesso'; // Retorna 'success' se o login for bem-sucedido
            exit();
        } else {
            echo 'fail';
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
    <link rel="stylesheet" type="text/css" href="./src/view/assets/css/stylelogin.css">


</head>

<body>
<div style="color: red;" id="mensagem"></div>
<div style="color: red;" id="mensagem2"></div>

    <div style="color: red;" id="error-message"></div>

    <div class="container" id="container">
        <div class="form-container sign-up">
            <form id="cadastroForm" method="post">
                <h1>Cadastro de usuário</h1>
                <input type="text" id="nome" name="nome" required placeholder="Nome">
                <input type="text" id="login" name="login" required placeholder="E-mail">
                <input type="password" id="senha" name="senha" required placeholder="Senha">

                <input type="submit" value="Cadastrar" class="button">

            </form>

        </div>


        <div class="form-container sign-in">
            <form id="formLogin" method="post" action="login.php">
                <h1>Entrar</h1>
                <input type="text" id="login" name="login" required placeholder="E-mail"><br><br>
                <input type="password" id="senha" name="senha" required placeholder="Senha"><br><br>
                <a href="#">Esqueceu sua senha ?</a>
                <input type="submit" value="Entrar" class="button">
                <a href="index.php">Voltar para a página inicial</a>
                <br>
            </form>
        </div>
        <div class="toggle-container">
            <div class="toggle">
                <div class="toggle-panel toggle-left">
                    <h1>Bem-vindo novamente!!</h1>
                    <p>Entre em sua conta para utilizar todas as funções de nossa loja!</p>
                    <button class="hidden" id="signInBtn">Entrar</button>
                </div>
                <div class="toggle-panel toggle-right">
                    <h1>Olá, amigo!</h1>
                    <p>Registre sua própria conta e desfrute de todas as funções de nossa loja!</p>
                    <button class="hidden" id="signUpBtn">Cadastre-se</button>
                </div>
            </div>
        </div>
    </div>



    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelector("#cadastroForm").addEventListener("submit", function(e) {
                e.preventDefault();
                var nome = document.querySelector("#nome").value;
                var login = document.querySelector("#login").value;
                var senha = document.querySelector("#senha").value;

                var xhr = new XMLHttpRequest();
                xhr.open("POST", "login.php", true);
                xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function() {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        var response = JSON.parse(xhr.responseText);
                        if (response.status == "success") {
                            document.querySelector("#mensagem2").innerHTML = "<p style='color:green;'>" + response.mensagem2 + "</p>";
                        } else {
                            document.querySelector("#mensagem").innerHTML = "<p style='color:red;'>" + response.mensagem + "</p>";
                        }
                    }
                };
                xhr.send("nome=" + nome + "&login=" + login + "&senha=" + senha);
            });
        });
    </script>


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
                        if (response === 'sucesso') {
                            window.location.href = 'index.php';
                        } else if (response === 'success') {

                        } else {
                            $('#error-message').text(response);
                        }
                    }
                });
            });
        });
    </script>


    <script src="./src/view/assets/js/scriptlogin.js"></script>

</body>

</html>