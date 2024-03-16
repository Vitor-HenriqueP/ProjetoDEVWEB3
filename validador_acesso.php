<?php
//Codigo para a validação do acesso do usuario.
session_start();//Cria a sessão
//Verifica se o usuario existe, se sim, atribui ao usuario o profile id e as permissões[...]
//[...] atribuidas a aquele profile id, se a condição for falsa, será atribuida um valor nulo.
if (isset($_SESSION['usuario']) && is_object($_SESSION['usuario'])) {
    $user = $_SESSION['usuario'];
    $_SESSION['profile_id'] = $user->profile_id;
} else {
    $_SESSION['profile_id'] = null;
}
//Se o usuario não for autenticado não sera possivel realizar o login.
if (!isset($_SESSION['autenticar']) || $_SESSION['autenticar'] != 'SIM') {
    header('Location: http://localhost/cadastroAlunos/index.php?login=erro');
    exit(); 
    
}
?>
