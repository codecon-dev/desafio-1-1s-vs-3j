<?php

require __DIR__ . '/vendor/autoload.php';

//controladores
use Controllers\FileController;
use Controllers\UsuariosController;

//regras de negócio

// Obter a URL amigável após a reescrita
$url = isset($_GET['url']) ? $_GET['url'] : '';

// Tratar a URL para identificar o recurso e a ação
if (!empty($url)) {
    $url = rtrim($url, '/');
    $url_parts = explode('/', $url);

    // Primeiro parâmetro é o recurso (ex: usuario, veiculo)
    $recurso = isset($url_parts[0]) ? $url_parts[0] : '';

    // Segundo parâmetro é a ação (ex: cadastrarUsuario, listarVeiculos)
    $acao = isset($url_parts[1]) ? $url_parts[1] : 'index';

    // Instanciar o controlador e a RN necessária
    switch ($recurso) {
        case 'file':            
            $controller = new FileController();
            break;  
        case 'usuarios':
            $controller = new UsuariosController();
            break;      
        default:
            $controller = null;
            break;
    }

    // Verificar se o controlador foi instanciado e a ação existe
    if ($controller && method_exists($controller, $acao)) {
        // Passar parâmetros adicionais, se necessário
        $params = array_slice($url_parts, 2);
        call_user_func_array([$controller, $acao], $params);
    } else {
        echo "404 não encontrado - método não existe no controlador";
    }
} else {
    var_dump($url);
    exit;
}

?>


