<?php
require_once "../../validador_acesso.php";

$alunos = isset($_SESSION['alunos']) ? $_SESSION['alunos'] : [];

?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <title>Consulta de Alunos</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  <style>
    .card-consultar-aluno {
      padding: 30px 0 0 0;
      width: 100%;
      margin: 0 auto;
    }

    .navbar {
      background-color: #3498db;
      align-items: center;
      justify-content: center;
    }

    .navbar-brand {
      background-color: white;
      border-radius: 8px;
      padding: 6px;
      transition: box-shadow 0.3s ease-in-out;
      display: flex;
    }

    .navbar-brand:hover {
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .card {
      background-color: #3498db;
      border: 1px solid #ddd;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      transition: box-shadow 0.3s ease-in-out;
    }

    .card:hover {
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    }

    .card-header {
      background-color: #3498db;
      color: #ffffff;
      font-size: 24px;
      text-align: center;
      padding: 15px;
    }

    .card-body {
      background-color: white;

      padding: 20px;
    }

    .btn-info {
      background-color: #3498db;
      color: #ffffff;
      transition: background-color 0.3s ease-in-out;
    }

    .btn-info:hover {
      background-color: #2980b9;
    }

    .btn-sair {
      background-color: #f1c40f;
      /* Cor amarela para o botão de sair */
      color: #ffffff;
      transition: background-color 0.3s ease-in-out;
    }

    .btn-sair:hover {
      background-color: #d4ac0d;
    }
  </style>
</head>

<body>
  <nav class="navbar navbar-light ">
    <a class="navbar-brand" href="#">
      <img src="../../logo.png" width="30" height="30" class="d-inline-block align-top" alt="">
      Consulta de Alunos
    </a>
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" href="../../logoff.php">
          <button class="btn btn-sair">SAIR</button>
        </a>
      </li>
    </ul>
  </nav>

  <div class="container">
    <div class="row">
      <div class="card-consultar-aluno">
        <div class="card">
          <div class="card-header">
            <h5 class="card-title">Lista de Alunos</h5>
          </div>

          <div class="card-body">
            <?php foreach ($alunos as $alunoKey => $aluno) { ?>
              <?php if (is_array($aluno)) { ?>

                <div class="card mb-3 bg-light">
                  <div class="card-body">
                    <h6 class="card-title">Nome: <?= $aluno['nome'] ?></h6>
                    <h6 class="card-title">Matrícula: <?= $aluno['matricula'] ?></h6>
                    <h6 class="card-title">Curso: <?= $aluno['curso'] ?></h6>
                    <div class="row">
                      <div class="col">
                        <a href="editar_aluno.php?id=<?= $alunoKey ?>" class="btn btn-sm btn-info">Editar</a>
                        <a href="excluir_aluno.php?id=<?= $alunoKey ?>" class="btn btn-sm btn-danger">Excluir</a>
                      </div>
                    </div>
                  </div>
                </div>
              <?php } ?>
            <?php } ?>
            <div class="row mt-5" style="display: flex; justify-content: center;">
              <div class="col-6">
                <a class="btn btn-lg btn-warning btn-block"  href="home.php">Voltar</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>

</html>
