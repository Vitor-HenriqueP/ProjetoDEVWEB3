<?php
class Usuario
{
    protected $conn;

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



    public function autenticarUsuario($login, $senha)
    {
        $usuario = $this->findByLogin($login);
        if ($usuario && password_verify($senha, $usuario['senha'])) {
            return $usuario;
        }
        return false;
    }
}


class Usuario_Padrao extends Usuario
{
    public function cadastrarUsuario($nome, $login, $senha)
    {
        $tipo_usuario = 2; // Definindo o tipo de usuário como 2
        $query = "INSERT INTO usuarios (nome, login, senha, tipo_usuario) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sssi", $nome, $login, $senha, $tipo_usuario); // 'sssi' indica que são três strings e uma int
        return $stmt->execute();
    }
}
class Usuario_Adm extends Usuario
{
    public function cadastrarUsuario($nome, $login, $senha)
    {
        
        $tipo_usuario = 1; // Definindo o tipo de usuário como 1
        $query = "INSERT INTO usuarios (nome, login, senha, tipo_usuario) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sssi", $nome, $login, $senha, $tipo_usuario); // 'sssi' indica que são três strings e uma int
        return $stmt->execute();
    }
}
