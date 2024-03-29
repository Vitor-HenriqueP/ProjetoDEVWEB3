<?php
include 'conexao.php'; // Assumindo que este arquivo inclui a conexão com o banco de dados
include 'src/models/User.php';

$usuario = new Usuario_Padrao($conn);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
    $login = filter_input(INPUT_POST, 'login', FILTER_SANITIZE_STRING);
    $senha = filter_input(INPUT_POST, 'senha', FILTER_SANITIZE_STRING);

    // Validação de campos
    if (empty($nome) || empty($login) || empty($senha)) {
        $mensagem = "Dados inválidos.";
        echo json_encode(array("status" => "error", "mensagem" => $mensagem));
    } else {
        // Verificar se o login já está em uso
        $stmt_verificar = $conn->prepare("SELECT id FROM usuarios WHERE login = ?");
        $stmt_verificar->bind_param("s", $login);
        $stmt_verificar->execute();
        $result_verificar = $stmt_verificar->get_result();

        if ($result_verificar->num_rows > 0) {

            $mensagem = "Login já está em uso.";
            echo json_encode(array("status" => "error", "mensagem" => $mensagem));
            exit();
        } else {
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            if ($usuario->cadastrarUsuario($nome, $login, $senha_hash)) {
                // Cadastro bem-sucedido
                $mensagem = "Cadastro bem-sucedido.";
                echo json_encode(array("status" => "success", "mensagem" => $mensagem));
                exit();
            } else {
                // Caso ocorra algum erro no cadastro
            }
        }
    }
}
