<?php

namespace Utils;

class HttpResponses{

    public static function json($statusCode, $body = [], $headers = []){
        http_response_code($statusCode);
        header('Content-Type: application/json');

        foreach($headers as $key => $value){
            header("$key: $value");
        }

        echo json_encode($body);
        exit;
    }

    public static function ok($body = [], $headers = []){
        self::json(200, $body, $headers);
    }

    public static function created($body = [], $headers = []){
        self::json(201, $body, $headers);
    }

    public static function badRequest($mensagem = 'Requisição inválida'){
        self::json(400, ['erro' => $mensagem]);
    }

    public static function unautorized($mensagem = 'Não autorizado'){
        self::json(401, ['erro' => $mensagem]);
    }

    public static function notFound($mensagem = 'Recurso não encontrado'){
        self::json(404, ['erro' => $mensagem]);
    }

    public static function internalError($mensagem = 'Erro interno no servidor'){
        self::json(500, ['erro' => $mensagem]);
    }

}

?>