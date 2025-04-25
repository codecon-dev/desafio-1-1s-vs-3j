<?php

namespace Utils;

class HttpResponses{

    public static function json($statusCode, $body = [], $headers = []){
        http_response_code($statusCode);

        header('Content-Type: application/json; charset=utf-8');    
        
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');        
        
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');        
        
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: DENY');
        header('X-XSS-Protection: 1; mode=block');

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