<?php

namespace Controllers;

require_once __DIR__ . '/../../vendor/autoload.php';

use Utils\HttpResponses;
use Rn\RnUsuarios;
use Models\Usuario;
use Utils\TimeControlServices;

class UsuariosController{

    function superUsers(){
        $rnUsuarios = new RnUsuarios();

        TimeControlServices::startReq();

        $json = $rnUsuarios->superUsers();

        TimeControlServices::finalReq();

        $requisition = [
            'Time stamp: ' => TimeControlServices::timeStamp(),
            'Tempo de processamento: ' =>TimeControlServices::processTime(),
            'Body: ' => $json
        ];

        HttpResponses::ok($requisition);        
    }

}

?>