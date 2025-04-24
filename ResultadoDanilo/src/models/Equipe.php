<?php

namespace models;

class Equipe{
    private $nome;
    private $lider;

    function __construct($nome, $lider){
        $this->setNome($nome);
        $this->setLider($lider);
    }

    //setters
    function setNome($nome){
        $this->nome = $nome;
    }

    function setLider($lider){
        $this->lider = $lider;
    }

    //getters
    function getNome(){
        return $this->nome;
    }

    function getLider(){
        return $this->lider;
    }
}

?>