<?php

namespace Models;

use JsonSerializable;

class Equipe implements JsonSerializable{
    private $nome;
    private $lider;
    private $projetos = [];

    function __construct($nome, $lider, $projetos = []){
        $this->setNome($nome);
        $this->setLider($lider);
        $this->setProjetos($projetos);
    }

    //setters
    function setNome($nome){
        $this->nome = $nome;
    }

    function setLider($lider){
        $this->lider = $lider;
    }

    function setProjetos($projetos = []){
        $this->projetos = $projetos;
    }

    //getters
    function getNome(){
        return $this->nome;
    }

    function getLider(){
        return $this->lider;
    }

    function getProjetos(){
        return $this->projetos;
    }

    //toString
    function toString(){
        return
        "<br><strong>Equipe</strong>".
        "<br>Nome: ".$this->getNome().
        "<br>Lider: ".$this->getLider().
        "<br><strong>Projetos</strong>";
        //iteração sobre a lista projetos
        
    }

    //jsonSerializable
    public function jsonSerialize(): mixed {
        return [
            'nome' => $this->getNome(),
            'lider' => $this->getLider(),
            'projetos' => $this->getProjetos()
        ];
    }


}

?>