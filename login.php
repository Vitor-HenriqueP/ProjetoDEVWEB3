<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="./src/view/assets/css/stylelogin.css">
    <title>Login</title>
</head>

<body>
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
            echo "<p style='color:red;'>Dados inválidos.</p>";
        } else {
            // Verificar se o login já está em uso
            $stmt_verificar = $conn->prepare("SELECT id FROM usuarios WHERE login = ?");
            $stmt_verificar->bind_param("s", $login);
            $stmt_verificar->execute();
            $result_verificar = $stmt_verificar->get_result();

            if ($result_verificar->num_rows > 0) {
                echo "<p style='color:red;'>Dados inválidos.</p>";
            } else {
                $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
                if ($usuario->cadastrarUsuario($nome, $login, $senha_hash)) {
                    // Cadastro bem-sucedido
                    header('Location: login.php?cadastro=success');
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
            if (password_verify($senha, $usuarioEncontrado['senha'])) {
                // Login bem-sucedido
                $_SESSION['id'] = $usuarioEncontrado['id'];
                $_SESSION['nome'] = $usuarioEncontrado['nome']; // Adicione esta linha para armazenar o nome do usuário na sessão
                $_SESSION['login'] = $login;
                $_SESSION['tipo_usuario'] = $usuarioEncontrado['tipo_usuario']; // Corrigido para 'tipo_user'
                header('Location: index.php');
                exit();
            } else if ($result->num_rows == 0) {
                $erro = "Login ou senha incorretos";
            }
        } 
    }
    ?>
    <div class="container" id="container">
        <div class="form-container sign-up">
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <h1>Cadastro de usuário</h1>
                <input type="text" id="nome" name="nome" required placeholder="Nome">
                <input type="text" id="login" name="login" required placeholder="E-mail">
                <input type="password" id="senha" name="senha" required placeholder="Senha">

                <input type="submit" value="Cadastrar" class="button">
            </form>
        </div>
        <?php if (isset($erro)) {
            echo "<p>$erro</p>";
        } ?>
        <div class="form-container sign-in">
            <form method="post" action="login.php">
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
    <div id="popup" class="popup">
        <div class="popup-content">
            <span class="close">&times;</span>
            <p>Cadastro bem-sucedido!</p>
        </div>
    </div>
    <script src="./src/view/assets/js/scriptlogin.js"></script>
</body>

</html>