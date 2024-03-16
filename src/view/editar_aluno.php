<?php
session_start();

$alunoId = $_GET['id'];
$alunos = isset($_SESSION['alunos']) ? $_SESSION['alunos'] : [];

$aluno = $alunos[$alunoId];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Atualizar os dados do aluno com base no formulário enviado
    $aluno['nome'] = $_POST['nome'];
    $aluno['matricula'] = $_POST['matricula'];
    $aluno['curso'] = $_POST['curso'];

    $alunos[$alunoId] = $aluno;
    $_SESSION['alunos'] = $alunos;

    header('Location: consultar_alunos.php');
    exit;
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Editar Aluno</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>

<body>
    <div class="container mt-5">
        <h2>Editar Aluno</h2>
        <form method="POST">
            <div class="form-group">
                <label for="nome">Nome:</label>
                <input type="text" class="form-control" id="nome" name="nome" value="<?= $aluno['nome'] ?>" required>
            </div>
            <div class="form-group">
                <label for="matricula">Matrícula:</label>
                <input type="text" class="form-control" id="matricula" name="matricula" value="<?= $aluno['matricula'] ?>" required>
            </div>
            <div class="form-group">
                <label for="curso">Curso:</label>
                <input type="text" class="form-control" id="curso" name="curso" value="<?= $aluno['curso'] ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Salvar</button>
            <a class="btn btn-lg btn-warning btn-block"  href="home.php">Voltar</a>

            <div class="row mt-5">
  </div>

        </form>
    </div>
</body>

</html>