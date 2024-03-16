<?php

?>

<html>

<head>
    <meta charset="utf-8">
    <title>Students System</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <style>
        .card-login {
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
        }

        body {
            background-color: #f0f0f0;
            margin: 0;
            font-family: Arial, sans-serif;
            overflow-y: hidden; 
        }

        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
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
            border-radius: 8px;
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
            transition: width 0.3s ease-in-out;
        }

        .form-control:focus {
            width: calc(100% - 20px);
        }

        .btn-info {
            background-color: #3498db;
            color: #ffffff;
            transition: background-color 0.3s ease-in-out;
        }

        .btn-info:hover {
            background-color: #2980b9;
        }

        .text-danger {
            color: #e74c3c;
            margin-top: 10px;
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
            align-items: center;
        }

        .navbar-brand img {
            margin-right: 10px;
        }

        .navbar-brand:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-light">
        <a href="#" class="navbar-brand">
            <img src="logo.png" width="30px" height="30px" class="d-inline-block align-top" alt="">
            Students System
        </a>
    </nav>
    <div class="container">
        <div class="card-login">
            <div class="card">
                <div class="card-header">
                    Login
                </div>
                <div class="card-body">
                    <form action="valida_login.php" method="post">
                        <div class="form-group">
                            <input name="email" type="email" class="form-control" placeholder="E-mail">
                        </div>
                        <div class="form-group">
                            <input name="senha" type="password" class="form-control" placeholder="Senha">
                        </div>
                        <?php if (isset($_GET['login']) && $_GET['login'] == 'erro') { ?>
                            <div class="text-danger">
                                Usuário ou senha inválido(s)
                            </div>
                        <?php } ?>
                        <button class="btn btn-lg btn-info btn-block" type="submit">Entrar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
