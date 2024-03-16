<?php

require_once "../cadastroAlunos/src/models/User.php";

class LoginController
{
    private $users;
    //Função que define quem pode acessar, cadastrar e consultar os alunos.
    public function __construct()
    {
        $this->users = [
            new User(1, 'adm@teste.com.br', '1234', 1),
            new User(2, 'user@teste.com.br', '1234', 1),
            new User(3, 'marsio@teste.com.br', '1234', 2)
        ];
    }
    //Autenticação do Login, verificando a senha e e-mail.
    public function autenticar($email, $password)
    {
        $email = trim($email);
        $password = trim($password);

        foreach ($this->users as $user) {
            if (($user->email == $email) && $user->verificarSenha($password)) {
                $_SESSION['autenticar'] = 'SIM';
                $_SESSION['id'] = $user->id;
                $_SESSION['profile_id'] = $user->profile_id;
                header('Location: ../cadastroAlunos/src/view/home.php');
                exit;
            }
        }
        $_SESSION['autenticar'] = 'NAO';
        header('Location: index.php?login=erro');
    }
}
?>