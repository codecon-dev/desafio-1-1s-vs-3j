<?php

namespace Controllers;

use Utils\HttpResponses;
use Utils\TimeControlServices;

class EvaluationController{

    public function avaliar(){
        TimeControlServices::startReq();
        
        $testes = [   
            'GET /superUsers' => 'http://localhost/desafio-1-1s-vs-3j/usuarios/superusers',        
            'GET /top-countries' => 'http://localhost/desafio-1-1s-vs-3j/usuarios/topCountries',
            'GET /usuarios-por-equipe' => 'http://localhost/desafio-1-1s-vs-3j/usuarios/teamInsights',
            'GET /logins-por-data' => 'http://localhost/desafio-1-1s-vs-3j/login/activeUsersPerDay',
        ];

        $relatorio = [];

        foreach ($testes as $nome => $url) {
            $inicio = microtime(true);

            $resposta = @file_get_contents($url);
            $fim = microtime(true);

            $status = $http_response_header[0] ?? '';
            preg_match('/HTTP\/\d+\.\d+ (\d+)/', $status, $matches);
            $codigo = $matches[1] ?? 0;

            $jsonValido = json_decode($resposta, true);
            $jsonStatus = json_last_error() === JSON_ERROR_NONE;

            $relatorio[] = [
                'endpoint' => $nome,
                'statusCode' => (int)$codigo,
                'respostaValida' => $jsonStatus,
                'tempoRespostaMs' => round(($fim - $inicio) * 1000, 2)
            ];
        }

        TimeControlServices::finalReq();

        $body = [
            'Time stamp' => TimeControlServices::timeStamp(),
            'Tempo de processamento' => TimeControlServices::processTime(),
            'resultado' =>$relatorio
        ];

        HttpResponses::ok($body);

    }

}
