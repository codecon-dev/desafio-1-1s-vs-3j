<?php

namespace Rn;

require_once __DIR__ . '/../../vendor/autoload.php';

use Utils\JsonServices;

class RnLogin{

    function filtrarLoginPorData() {
        $json = JsonServices::lerUltimoJson();
        $loginsPorData = [];
    
        // Pega o valor de 'min' na URL, se existir
        $min = isset($_GET['min']) ? (int)$_GET['min'] : 0;
    
        foreach ($json as $usuario) {
            foreach ($usuario['logs'] as $log) {
                if ($log['acao'] === 'login') {
                    $data = $log['data'];
    
                    if (!isset($loginsPorData[$data])) {
                        $loginsPorData[$data] = 0;
                    }
    
                    $loginsPorData[$data]++;
                }
            }
        }
    
        // Aplica o filtro se necessário
        if ($min > 0) {
            $loginsPorData = array_filter($loginsPorData, function($qtd) use ($min) {
                return $qtd >= $min;
            });
        }
    
        return $loginsPorData;
    }
    

}

?>