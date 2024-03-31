<?php
include 'conexao.php'; // Assumindo que este arquivo inclui a conexão com o banco de dados
include 'src/models/User.php';

$usuario = new Usuario_Adm($conn);

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
            echo json_encode(array("status" => "error", "mensagem" => $mensagem));
            exit();
        } else {
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            if ($usuario->cadastrarUsuario($nome, $login, $senha_hash)) {
                // Cadastro bem-sucedido
                $mensagem = "Cadastro bem-sucedido.";
                echo json_encode(array("status" => "success", "mensagem" => $mensagem));
                exit();
            } else {
                // Caso ocorra algum erro no cadastro
            }
        }
    }
}
if (isset($_POST['excluir']) && isset($_POST['id'])) {
    $id = $_POST['id'];
    if ($usuario->excluirUsuario($id)) {
        echo "<script>alert('Administrador excluido') </script>";
    } else {
        echo "<script>alert('erro ao excluir Administrador') </script>";

    }
}





$administradores = $usuario->listarAdministradores();

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="./src/view/assets/css/stylecadastroadm.css">
    <title>Cadastro de administrador</title>
    <script src="src/view/assets/js/scriptCadastroAdm.js"></script>

</head>

<body>
    <div id="mensagem"></div>

    <div class="container" id="container">
        <div class="form-container sign-up">
            <form method="post" id="formCadastroAdm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <h1>Cadastro de Usuário Administrador</h1>
                <input type="text" id="nome" name="nome" required placeholder="Nome">
                <input type="text" id="login" name="login" required placeholder="E-mail">
                <input type="password" id="senha" name="senha" required placeholder="Senha">
                <input type="submit" value="Cadastrar">
            </form>
        </div>
        <div class="form-container sign-in">
            <form id="excluiAdm" method="post">
                <h1>Administradores existentes.</h1>
                <?php foreach ($administradores as $admin) : ?>
                    <div class="background-container">
                        <p>Administrador:</p>
                        <p><?php echo substr($admin['nome'], 0, 5); ?></p>
                        <p><?php echo substr($admin['login'], 0, 5); ?></p>
                    </div>
                    <input type="hidden" name="id" value="<?php echo $admin['id']; ?>">
                    <input type="submit" name="excluir" value="Excluir" class="button">
                <?php endforeach; ?>
                <a href="index.php">Voltar para a página inicial</a>
            </form>
        </div>
        <div class="toggle-container">
            <div class="toggle">
                <div class="toggle-panel toggle-left">
                    <h1>Lista de admnistradores</h1>
                    <p>Deseja visualizar ou excluir algum admnistrador existente?</p>
                    <button class="hidden" id="signInBtn">Visualizar</button>
                </div>
                <div class="toggle-panel toggle-right">
                    <h1>Cadastre um novo administrador</h1>
                    <p>Existe um novo administrador na loja?</p>
                    <button class="hidden" id="signUpBtn">Cadastre-o</button>
                </div>
            </div>
        </div>
    </div>
    <script src="./src/view/assets/js/scriptlogin.js"></script>
</body>

</html>