<?php

require __DIR__ . '/vendor/autoload.php';

//controladores

use Controllers\EvaluationController;
use Controllers\FileController;
use Controllers\LoginController;
use Controllers\UsuariosController;
use Utils\HttpResponses;

// Obter a URL amigável após a reescrita
$url = isset($_GET['url']) ? $_GET['url'] : '';

// Tratar a URL para identificar o recurso e a ação
if (!empty($url)) {
    $url = rtrim($url, '/');
    $url_parts = explode('/', $url);
    
    $recurso = isset($url_parts[0]) ? $url_parts[0] : '';
    
    $acao = isset($url_parts[1]) ? $url_parts[1] : 'index';
    
    switch ($recurso) {
        case 'file':            
            $controller = new FileController();
            break;  
        case 'usuarios':
            $controller = new UsuariosController();
            break;  
        case 'login':
            $controller = new LoginController();
            break; 
        case 'evaluation':
            $controller = new EvaluationController();
            break;
        default:
            $controller = null;
            break;
    }

    // Verificar se o controlador foi instanciado e a ação existe
    if ($controller && method_exists($controller, $acao)) {
        
        $params = array_slice($url_parts, 2);
        call_user_func_array([$controller, $acao], $params);
    } else {
        HttpResponses::notFound();
    }
} else {
    HttpResponses::notFound();
    exit;
}

?>


