<?php

class Produto {
    private $nome;
    private $descricao;
    private $preco;
    private $categoria;
    private $imagem;
    private $slug;

    public function __construct($nome, $descricao, $preco, $categoria, $imagem = null) {
        $this->nome = $nome;
        $this->descricao = $descricao;
        $this->preco = $preco;
        $this->categoria = $categoria;
        $this->imagem = $imagem;
        $this->slug = $this->slugify($nome);
    }

    public function getNome() {
        return $this->nome;
    }

    public function getDescricao() {
        return $this->descricao;
    }

    public function getPreco() {
        return $this->preco;
    }

    public function getCategoria() {
        return $this->categoria;
    }

    public function getImagem() {
        return $this->imagem;
    }

    public function getSlug() {
        return $this->slug;
    }

    private function slugify($text) {
        $text = preg_replace('/[^\pL\d]+/u', '-', $text);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = strtolower($text);
        $text = preg_replace('/[^-\w]+/', '', $text);
        $text = trim($text, '-');

        $randomString = substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(150 / strlen($x)))), 1, 150);
        $text = $text . '-' . $randomString;

        return $text;
    }
}
?>
