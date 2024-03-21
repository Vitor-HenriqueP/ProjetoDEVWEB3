<?php
class Usuario
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    private function findByLogin($login)
    {
        $query = "SELECT * FROM usuarios WHERE login = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('s', $login);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function cadastrarUsuario($nome, $login, $senha)
    {
        $query = "INSERT INTO usuarios (nome, login, senha) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sss", $nome, $login, $senha); // 'sss' indica que são três strings
        return $stmt->execute();
    }

    public function autenticarUsuario($login, $senha)
    {
        $usuario = $this->findByLogin($login);
        if ($usuario && password_verify($senha, $usuario['senha'])) {
            return $usuario;
        }
        return false;
    }
}
?>