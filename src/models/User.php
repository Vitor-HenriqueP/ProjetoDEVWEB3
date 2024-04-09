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
        $query = "INSERT INTO usuarios (nome, login, senha, tipo_usuario) VALUES (?, ?, ?, 2)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sss", $nome, $login, $senha); 
        return $stmt->execute();
    }
}

class Usuario_Adm extends Usuario
{
    public function cadastrarUsuario($nome, $login, $senha)
    {
        $query = "INSERT INTO usuarios (nome, login, senha, tipo_usuario) VALUES (?, ?, ?, 1)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sss", $nome, $login, $senha);
        return $stmt->execute();
    }

    public function excluirUsuario($id)
    {
        $query = "DELETE FROM usuarios WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }


    public function listarAdministradores()
    {
        $query = "SELECT * FROM usuarios WHERE tipo_usuario = 1";
        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}

class Usuario_Master extends Usuario
{

    public $tipo_usuario = 3;

}
