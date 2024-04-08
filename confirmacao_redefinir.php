<?php
include './conexao.php'; // Inclua o arquivo de conexão
session_start();

$mensagem = "Dados redefinidos.";
        echo '<script>window.setTimeout(function() { window.location.href = "user_config.php"; }, 1000);</script>';
  
?>

<!DOCTYPE html>
<html>

<head>
    <link rel="shortcut icon" type="image/png" href="./src/view/assets/imagens/cart2.png">
    <title>Estação Digital | Carrinho</title>
    <style>
        .card-mensagem {
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px;
            margin: 20px auto;
            max-width: 400px;
            background-color: #f9f9f9;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: <?php echo isset($mensagem) ? 'block' : 'none'; ?>;
        }
    </style>
</head>

<body>
    <div class="card-mensagem" id="mensagem"><?php echo $mensagem; ?></div>
</body>

</html>