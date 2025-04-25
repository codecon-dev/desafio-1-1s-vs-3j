<?php

namespace Controllers;

class EvaluationController{

    public function avaliar(){
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

        header('Content-Type: application/json');
        echo json_encode([
            'avaliadoEm' => date('Y-m-d H:i:s'),
            'resultado' => $relatorio
        ], JSON_PRETTY_PRINT);
    }

}
