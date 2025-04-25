<?php

namespace Models;

require_once __DIR__ . '/../../vendor/autoload.php';

use JsonSerializable;

class Log implements JsonSerializable{

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

    //Método json serializable
    public function jsonSerialize() :mixed{
        return [
            'data' => $this->getData(),
            'acao' => $this->getAcao()
        ];
    }
}

?>