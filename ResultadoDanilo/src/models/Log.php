<?php

namespace models;

class Log{
    private $data;
    private $acao;

    function __construct($data, $acao){
        $this->setData($data);
        $this->setAcao($acao);
    }

    function setData($data){
        $this->data = $data;
    }

    function setAcao($acao){
        $this->acao = $acao;
    }

    //getters
    function getData(){
        return $this->data;
    }

    function getAcao(){
        return $this->acao;
    }
}

?>