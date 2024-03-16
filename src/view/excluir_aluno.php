<?php
require_once "../../validador_acesso.php";

session_start();
//Metodo utilizado para realizar a exclusão do aluno.
$alunoId = $_GET['id'];
$alunos = isset($_SESSION['alunos']) ? $_SESSION['alunos'] : [];

unset($alunos[$alunoId]);
$_SESSION['alunos'] = $alunos;

header('Location: consultar_alunos.php');
exit;
?>
