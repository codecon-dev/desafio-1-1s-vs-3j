<?php

namespace Models;

use JsonSerializable;

class Usuario implements JsonSerializable{
    private $id;
    private $nome;
    private $idade;
    private $score;
    private $ativo;
    private $pais;
    private $equipe = [];
    private $logs = [];

    function __construct($id, $nome, $idade, $score, $ativo, $pais, $equipe, $logs = []){
        $this->setIdUsuario($id);
        $this->setNome($nome);
        $this->setIdade($idade);
        $this->setScore($score);
        $this->setAtivo($ativo);
        $this->setPais($pais);
        $this->setEquipe($equipe);
        $this->setLogs($logs);
    }

    //setters
    function setIdUsuario($id){
        $this->id = $id;
    }

    function setNome($nome){
        $this->nome = $nome;
    }

    function setIdade($idade){
        $this->idade = $idade;
    }

    function setScore($score){
        $this->score = $score;
    }

    function setAtivo($ativo){
        $this->ativo = $ativo;
    }

    function setPais($pais){
        $this->pais = $pais;
    }

    function setEquipe($equipe){
        $this->equipe = $equipe;
    }

    function setLogs($logs){
        $this->logs = $logs;
    }

    //getter
    function getIdUsuario(){
        return $this->id;
    }

    function getNome(){
        return $this->nome;
    }

    function getIdade(){
        return $this->idade;
    }

    function getScore(){
        return $this->score;
    }

    function getAtivo(){
        return $this->ativo;
    }

    function getPais(){
        return $this->pais;
    }

    function getEquipe(){
        return $this->equipe;
    }

    function getLogs(){
        return $this->logs;
    }

    //toString
    function toString(){       
        
        return 
        "<br>ID usuario: ".$this->getIdUsuario().
        "<br>Nome: ".$this->getNome().
        "<br>Idade: ".$this->getIdade().
        "<br>Score: ".$this->getScore().
        "<br>Ativo: ".$this->getAtivo().
        "<br>País: ".$this->getPais()."<br>";        
    }

    //jsonSerializable
    public function jsonSerialize(): mixed {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'idade' => $this->idade,
            'score' => $this->score,
            'ativo' => $this->ativo,
            'pais' => $this->pais,
            'equipe' => $this->equipe,
            'logs' => $this->logs
        ];
    }

}

?>