<?php
//Codigo para validar o login, autenticando e-mail e senha.
session_start();
require_once "../cadastroAlunos/src/controllers/LoginController.php";

$loginController = new LoginController();
$loginController->autenticar($_POST['email'], $_POST['senha']);

?>