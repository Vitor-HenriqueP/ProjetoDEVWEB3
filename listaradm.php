<?php
include 'conexao.php'; // Assumindo que este arquivo inclui a conexão com o banco de dados
include 'src/models/User.php';

$usuario = new Usuario_Adm($conn);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar se é uma solicitação de exclusão
    if (isset($_POST['excluir']) && isset($_POST['id'])) {
        $id = $_POST['id'];
        // Chame o método para excluir o usuário administrador
        if ($usuario->excluirUsuario($id)) {
            // Redirecionamento após a exclusão bem-sucedida
            header("Location: listaradm.php");
            exit(); // Certifique-se de sair do script após o redirecionamento
        } else {
            echo "Erro ao excluir administrador";
        }
    }

    // Se não é uma solicitação de exclusão, pode ser uma solicitação de cadastro
    $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
    $login = filter_input(INPUT_POST, 'login', FILTER_SANITIZE_STRING);
    $senha = filter_input(INPUT_POST, 'senha', FILTER_SANITIZE_STRING);

    // Validação de campos
    if (empty($nome) || empty($login) || empty($senha)) {
        // Lógica de validação de campos vazios aqui
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

$administradores = $usuario->listarAdministradores();

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estação Digital | Lista de Administradores</title>
    <link rel="stylesheet" type="text/css" href="./src/view/assets/css/stylelistaradm.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="src/view/assets/js/scriptCadastroAdm.js"></script>

</head>

<body>
    <div class="header" id="header">
        <button onclick="toggleSidebar()" class="btn_icon_header">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-list" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z" />
            </svg>
        </button>
        <div class="logo_header">
            <a href="../../index.php"><img src="./src/view/assets/imagens/newlogo.png" alt="Logo Estação Digital" class="img_logo_header"></a>
        </div>
        <div class="navigation_header" id="navigation_header">
            <button onclick="toggleSidebar()" class="btn_icon_header">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                    <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z" />
                </svg>
            </button>
            <?php if (!isset($_SESSION['login'])) : ?>
                <a href="login.php"><span class="material-symbols-outlined">login</span></a>
            <?php endif; ?>
            <?php if (!isset($_SESSION['login']) || ($_SESSION['tipo_usuario'] == 2)) : ?>
                <a href="src/view/carrinho.php"><span class="material-symbols-outlined">shopping_cart</span></a>
            <?php endif; ?>
            <?php if (isset($_SESSION['login'])) : ?>
                <a href="user_config.php"><span class="material-symbols-outlined">person</span></a>
            <?php endif; ?>

            <?php if (isset($_SESSION['login'])) : ?>
                <form method="post" action="../../index.php">
                    <input type="hidden" name="logout" value="1">
                    <button type="submit" style="background: none; border: none; cursor: pointer;">
                        <a><span class="material-symbols-outlined" style="vertical-align: middle;">logout</span></a>
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
    <div class="container">
        <h1>Administradores existentes</h1>
        <?php foreach ($administradores as $admin) : ?>
            <div class="admin-item">
                <div class="admin-info">
                    <p><strong>Administrador:</strong> <?php echo $admin['nome']; ?></p>
                    <p><strong>Login:</strong> <?php echo $admin['login']; ?></p>
                </div>
                <span class="material-symbols-outlined delete-icon" onclick="confirmDelete(<?php echo $admin['id']; ?>)">delete</span>
                <form id="excluiAdm<?php echo $admin['id']; ?>" method="post" style="display: none;">
                    <input type="hidden" name="id" value="<?php echo $admin['id']; ?>">
                    <input type="hidden" name="excluir" value="1">
                </form>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="container">
        <a href="index.php">Voltar para a página inicial</a>
    </div>
    <script src="./src/view/assets/js/scriptloginjs"></script>
    <script>
        function confirmDelete(id) {
            if (confirm("Tem certeza que deseja excluir este administrador?")) {
                document.getElementById("excluiAdm" + id).submit();
            }
        }
    </script>
</body>

</html>