<?php
//Codigo home para a visualização da página inicial

require_once "../../validador_acesso.php";
?>

<html>

<head>
  <meta charset="utf-8" />
  <title>Students System</title>

  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

  <style>
    .card-home {
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
      padding: 20px;
      background-color: white;  

    }

    .form-group {
      position: relative;
      margin-bottom: 20px;
    }

    .form-control {
      width: 100%;
      padding: 10px;
      border: 1px solid #ddd;
      border-radius: 5px;
      transition: border-color 0.3s ease-in-out;
    }

    .form-control:focus {
      border-color: #2980b9;
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
      color: #ffffff;
      transition: background-color 0.3s ease-in-out;
    }

    .btn-sair:hover {
      background-color: #d4ac0d;
    }

  </style>
</head>

<body>

  <nav class="navbar navbar-light">
    <a class="navbar-brand" href="#">
      <img src="../../logo.png" width="30" height="30" class="d-inline-block align-top" alt="">
      Students System
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
      <div class="card-home">
        <div class="card">
          <div class="card-header">
            Menu
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-6 d-flex justify-content-center">
                <a href="cadastrar_alunos.php" style="display: block; text-align: center; color: black;">
                  <img src="../../cadastraAluno.png" width="70" height="70">
                  <br />
                  Cadastrar aluno.
                </a>
              </div>
              <div class="col-6 d-flex justify-content-center">
                <a style="display: block; text-align: center; color: black;" href="consultar_alunos.php">
                  <img src="../../consultaAluno.png" width="70" height="70">
                  <br />
                  Consulta de alunos.
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>

</html>
