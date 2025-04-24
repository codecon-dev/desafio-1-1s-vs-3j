<?php

namespace Controllers;

require_once __DIR__ . '/../../vendor/autoload.php';

use Utils\HttpResponses;
use Rn\RnUsuarios;
use Models\Usuario;

class UsuariosController{

    function superUsers(){
        $rnUsuarios = new RnUsuarios();
        $json = $rnUsuarios->superUsers();
        
        

        HttpResponses::ok($json);        
    }

}

?>