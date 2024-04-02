<?php
include 'conexao.php'; // Assumindo que este arquivo inclui a conexão com o banco de dados
include 'src/models/User.php';

$usuario = new Usuario_Adm($conn);
if (isset($_POST['excluir']) && isset($_POST['id'])) {
    $id = $_POST['id'];
    if ($usuario->excluirUsuario($id)) {
        echo "Administrador excluído";
    } else {
        echo "Erro ao excluir administrador";
    }
}

// Lista todos os administradores
$administradores = $usuario->listarAdministradores();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="./src/view/assets/css/stylelistadm.css">
    <title>Lista de Administradores</title>
</head>
<body>
<div class="header" id="header">
                <button onclick="toggleSidebar()" class="btn_icon_header">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-list" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z" />
                    </svg>
                </button>
                <div class="logo_header">
                    <a href="../../index.php"><img src="./assets/imagens/newlogo.png" alt="Logo Estação Digital" class="img_logo_header"></a>
                </div>
                <div class="navigation_header" id="navigation_header">
                    <button onclick="toggleSidebar()" class="btn_icon_header">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                            <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z" />
                        </svg>
                    </button>
                    <?php if (!isset($_SESSION['login'])) : ?>
                        <a href="../../login.php"><span class="material-symbols-outlined">login</span></a>
                    <?php endif; ?>
                    <?php if (!isset($_SESSION['login']) || ($_SESSION['tipo_usuario'] == 2)) : ?>
                        <a href="src/view/carrinho.php"><span class="material-symbols-outlined">shopping_cart</span></a>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['login'])) : ?>
                        <a href="../../user_config.php"><span class="material-symbols-outlined">person</span></a>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['login'])) : ?>
                        <form method="post" action="../../index.php">
                            <input type="hidden" name="logout" value="1">
                            <button type="submit"  style="background: none; border: none; cursor: pointer;">
                                <a><span class="material-symbols-outlined" style="vertical-align: middle;">logout</span></a>
                            </button>
                        </form>

                    <?php endif; ?>

                </div>
    <div class="container">
        <div>
        <h1>Lista de Administradores</h1>
        <ul class="admin-list">
            <?php foreach ($administradores as $adm): ?>
                <li class="admin-item">
                    <?php echo $adm['nome']; ?>
                    <form method="post" action="">
                        <input type="hidden" name="id" value="<?php echo $adm['id']; ?>">
                        <button type="submit" name="excluir" class="delete-btn">Excluir</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
        
    </div>
</body>
</html>
