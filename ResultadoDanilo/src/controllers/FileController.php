<?php

namespace Controllers;

require_once __DIR__ . '/../../vendor/autoload.php';

use Utils\HttpResponses;


class FileController{

    function envioArquivo(){

        $method = strtolower($_SERVER['REQUEST_METHOD']);

        if($method == 'post'){

            $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

            if(strpos($contentType, 'application/json') !== false){
                $jsonData = file_get_contents("php://input");

                if(json_decode($jsonData)){
                    $nomeArquivo = 'json_'.date('dmY_His').'.json';                    
                    $caminho = __DIR__ . '/../../jsons/';
                    $caminhoCompleto = $caminho . $nomeArquivo;

                    if(!file_exists($caminho)){
                        mkdir($caminho, 0777, true);
                    }

                    if(file_put_contents($caminhoCompleto, $jsonData)){
                        echo HttpResponses::ok([
                            "mensagem" => "Arquivo salvo com sucesso",
                            "arquivo" => $nomeArquivo
                        ]);
                    } else {
                        echo HttpResponses::internalError("Erro ao salvar o arquivo");
                    }
                } else {
                    echo HttpResponses::badRequest("JSON inválido");
                }
            } else {
                echo HttpResponses::internalError("Tipo de mídia inválida. Use application/json");
            }

        } else {
            echo HttpResponses::badRequest();
        }
    }

}


?>