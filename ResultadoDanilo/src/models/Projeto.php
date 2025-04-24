<?php

namespace Models;


class Projeto{
    private $nome;
    private $concluido;

    function __construct($nome, $concluido){
        $this->setNome($nome);
        $this->setConcluido($concluido);
    }

    function setNome($nome){
        $this->nome = $nome;
    }

    function setConcluido($concluido){
        $this->concluido = $concluido;
    }

    function getNome(){
        return $this->nome;
    }

    function getConcluido(){
        return $this->concluido;
    }

    //toString
    function toString(){
        return
        "<br>Nome: ".$this->getNome()." Concluído: ".$this->getConcluido();
    }

}

?>