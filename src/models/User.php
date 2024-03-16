<?php

class User{
    public $id;
    public $email;
    public $password;
    public $profile_id;
    //Função onde cria o usuario e atribui um ID ao perfil do mesmo.
    public function __construct($id, $email, $password, $profile_id) {
        $this->id = $id;
        $this->email = $email;
        $this->password = password_hash($password, PASSWORD_DEFAULT);
        $this->profile_id = $profile_id;
     }
     //Função para verificar a senha do User e conferir se está correta.
    public function verificarSenha($password){
        return password_verify($password, $this->password);
    }
}

?>