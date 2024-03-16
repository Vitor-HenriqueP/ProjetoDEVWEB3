<?php
class Aluno {
    public $nome;
    public $curso;
    public $matricula;
    //Função da classe aluno, aonde define os atributos do aluno.
    public function __construct($nome, $curso, $matricula) {
        $this->nome = $nome;
        $this->curso = $curso;
        $this->matricula = $matricula;
    }
}
?>
