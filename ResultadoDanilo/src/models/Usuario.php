<?php

namespace models;

class Usuario{
    private $id;
    private $nome;
    private $idade;
    private $score;
    private $ativo;
    private $pais;
    private $equipe = [];
    private $logs = [];

    function __construct($id, $nome, $idade, $score, $ativo, $pais, $equipe = [], $logs = []){
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

}

?>