<?php
include 'conexao.php';
include 'src/models/User.php'; // Supondo que o arquivo com a definição da classe Usuario seja 'Usuario.php'

$usuario = new Usuario($conn); // Criando uma instância da classe Usuario

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
    $login = filter_input(INPUT_POST, 'login', FILTER_SANITIZE_STRING);
    $senha = filter_input(INPUT_POST, 'senha', FILTER_SANITIZE_STRING);
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT); // Gerar hash da senha    

    // Verificar se o login já está em uso
    $stmt_verificar = $conn->prepare("SELECT id FROM usuarios WHERE login = ?");
    $stmt_verificar->bind_param("s", $login);
    $stmt_verificar->execute();
    $result_verificar = $stmt_verificar->get_result();

    if ($result_verificar->num_rows > 0) {
        echo "Login já está em uso. Por favor, escolha outro.";
    } else {
        // Preparar a query SQL usando um prepared statement
        $stmt = $conn->prepare("INSERT INTO usuarios (nome, login, senha) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nome, $login, $senha_hash);

        if ($stmt->execute()) {
            echo "Usuário cadastrado com sucesso!";
            header('Location: login.php');
        } else {
            echo "Erro ao cadastrar o usuário.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Usuário</title>
</head>

<body>
    <h1>Cadastro de Usuário</h1>
    <form method="post" action="cadastro.php">
        <label for="nome">Nome:</label><br>
        <input type="text" id="nome" name="nome" required><br><br>

        <label for="login">Login:</label><br>
        <input type="text" id="login" name="login" required><br><br>

        <label for="senha">Senha:</label><br>
        <input type="password" id="senha" name="senha" required><br><br>

        <input type="submit" value="Cadastrar">
        <a href="index.php">Voltar para a pagina inicial</a>
        <br>
        <a href="login.php">Voltar para a pagina de login</a>
    </form>
</body>

</html>
