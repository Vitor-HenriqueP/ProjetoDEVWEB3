<?php
include 'conexao.php'; 

session_start();

if (!isset($_SESSION['login'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $novoNome = $_POST['novo_nome'];

   
    if (!empty($novoNome)) {

        $stmt = $conn->prepare("UPDATE usuarios SET nome = ? WHERE id = ?");
        $stmt->bind_param("si", $novoNome, $_SESSION['id']);
        $stmt->execute();

        $_SESSION['nome'] = $novoNome;
        header('Location: confirmacao_redefinir.php');
        exit();
    } else {
        $erro = "Por favor, preencha o campo Novo Nome.";
    }
}

if (!isset($_SESSION['login'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $novaSenha = $_POST['nova_senha'];

    if (!empty($novaSenha)) {
        $stmt = $conn->prepare("UPDATE usuarios SET senha = ? WHERE id = ?");
        $stmt->bind_param("si", password_hash($novaSenha, PASSWORD_DEFAULT), $_SESSION['id']);
        $stmt->execute();
        header('Location: confirmacao_redefinir.php');
        exit();
    } else {
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
    <link rel="stylesheet" type="text/css" href="./src/view/assets/css/stylecard.css">
    <link rel="shortcut icon" type="image/png" href="./src/view/assets/imagens/cart2.png">
    <title>Estação Digital | Configurações de usuario.</title>
</head>

<body>
    <div class="container" id="container">
        <div class="form-container sign-up">
            <?php if (isset($erro)) {
                echo "<p>$erro</p>";
            } ?>
            <form method="post" action="confirmacao_redefinir.php">
                <h1>Redefinir nome</h1>
                <input value="<?php echo htmlspecialchars($_SESSION['nome']); ?>" type="text" id="novo_nome" name="novo_nome" required placeholder="Novo nome"><br><br>

                <input type="submit" value="Redefinir" class="button">

                <a href="index.php">Voltar para a tela de inicio</a>
            </form>
        </div>
        <div class="form-container sign-in">
            <?php if (isset($erro)) {
                echo "<p>$erro</p>";
            } ?>
            <form method="post" action="confirmacao_redefinir.php">
                <h1>Redefinir senha</h1>
                <input type="password" id="nova_senha" name="nova_senha" required placeholder="Nova senha"><br><br>

                <input type="submit" value="Redefinir" class="button">

                <a href="index.php">Voltar para a tela de inicio</a>
            </form>
        </div>
        <div class="toggle-container">
            <div class="toggle">
                <div class="toggle-panel toggle-left">
                    <h1>Redefinir senha</h1>
                    <p>Você pode alterar sua senha sem complicações, mas lembre-se de criar uma senha segura!</p>
                    <button class="hidden" id="signInBtn">Redefinir senha</button>
                </div>
                <div class="toggle-panel toggle-right">
                    <h1>Redefinir nome</h1>
                    <p>Você pode alterar seu nome a qualquer instante, lembre-se seja culto!</p>
                    <button class="hidden" id="signUpBtn">Redefinir nome</button>
                </div>
            </div>
        </div>
    </div>
    <script src="./src/view/assets/js/scriptlogin.js"></script>
</body>

</html>