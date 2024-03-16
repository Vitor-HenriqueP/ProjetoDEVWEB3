<?php
// registra_alunos.php
session_start();
require_once "../../validador_acesso.php";

// Criar um array privado para armazenar os alunos
if (!isset($_SESSION['alunos'])) {
    $_SESSION['alunos'] = [];
}

// Verificar se todos os campos foram preenchidos
if (empty($_POST['nome']) || empty($_POST['matricula']) || empty($_POST['curso'])) {
    echo "<script>alert('Todos os campos devem ser preenchidos!'); window.location.href = 'cadastrar_alunos.php';</script>";
    exit;
}

// Estamos trabalhando na montagem do texto
$nome = str_replace('#', '-', $_POST['nome']);
$matricula = str_replace('#', '-', $_POST['matricula']);
$curso = str_replace('#', '-', $_POST['curso']);

// Verificar se a matrícula já existe em outro aluno
$matriculaExistente = false;
foreach ($_SESSION['alunos'] as $alunoCadastrado) {
    if ($alunoCadastrado['matricula'] === $matricula) {
        $matriculaExistente = true;
        break;
    }
}

// Se a matrícula já existe, exibir um popup e redirecionar de volta para a página cadastrar_alunos.php
if ($matriculaExistente) {
    echo "<script>alert('Já existe um aluno cadastrado com essa matrícula!'); window.location.href = 'cadastrar_alunos.php';</script>";
    exit;
}

// Cadastrar um novo aluno como um array associativo
$aluno = [
    'id' => $_SESSION['id'],
    'nome' => $nome,
    'matricula' => $matricula,
    'curso' => $curso
];

// Adicionar o novo aluno ao array de alunos na sessão
$_SESSION['alunos'][] = $aluno;
$aluno = [];

// Exibir um popup de sucesso e redirecionar para a página cadastrar_alunos.php
echo "<script>alert('Aluno cadastrado com sucesso!'); window.location.href = 'home.php';</script>";
exit;
