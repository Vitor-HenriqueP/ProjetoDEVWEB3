<?php
include 'conexao.php'; 
include 'src/models/User.php';

$usuario = new Usuario_Padrao($conn);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
    $login = filter_input(INPUT_POST, 'login', FILTER_SANITIZE_STRING);
    $senha = filter_input(INPUT_POST, 'senha', FILTER_SANITIZE_STRING);

    if (empty($nome) || empty($login) || empty($senha)) {
    } else {
        $stmt_verificar = $conn->prepare("SELECT id FROM usuarios WHERE login = ?");
        $stmt_verificar->bind_param("s", $login);
        $stmt_verificar->execute();
        $result_verificar = $stmt_verificar->get_result();

        if ($result_verificar->num_rows > 0) {

            $mensagem = "Login já está em uso.";
            echo json_encode(array("status" => "error", "mensagem" => $mensagem));
            exit();
        } else {
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            if ($usuario->cadastrarUsuario($nome, $login, $senha_hash)) {
                $mensagem = "Cadastro bem-sucedido.";
                echo json_encode(array("status" => "success", "mensagem" => $mensagem));
                exit();
            } else {
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
        $senha_hash = $usuarioEncontrado['senha'];
        if (password_verify($senha, $senha_hash)) {
            $_SESSION['id'] = $usuarioEncontrado['id'];
            $_SESSION['nome'] = $usuarioEncontrado['nome'];
            $_SESSION['login'] = $login;
            $_SESSION['tipo_usuario'] = $usuarioEncontrado['tipo_usuario']; 

            $response = array("success" => true, "message" => "Login bem-sucedido");
        } else {
            $response = array("success" => false, "message" => "Login ou senha incorretos");
        }
    } else {
        $response = array("success" => false, "message" => "Login ou senha incorretos");
    }

    echo json_encode($response);
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estação Digital | Login</title>
    <link rel="stylesheet" type="text/css" href="./src/view/assets/css/stylelogin.css">
    <link rel="shortcut icon" type="image/png" href="./src/view/assets/imagens/cart2.png">


</head>

<body>
    <div id="mensagem"></div>
    <div id="message"></div>


    <div class="container" id="container">
        <div class="form-container sign-up">
            <form id="cadastroForm" method="post">
                <h1>Cadastro de usuário</h1>
                <input type="text" id="nome" name="nome" required placeholder="Nome">
                <input type="email" id="login" name="login" required placeholder="E-mail">
                <input type="password" id="senha" name="senha" required placeholder="Senha">

                <input type="submit" value="Cadastrar" class="button">

            </form>

        </div>


        <div class="form-container sign-in">
            <form method="post" id="formLogin" action="login.php">
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


    <script src="./src/view/assets/js/scriptlogin.js"></script>

</body>

</html>