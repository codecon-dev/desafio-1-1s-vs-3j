<?php

namespace Controllers;

require_once __DIR__ . '/../../vendor/autoload.php';

use Rn\RnLogin;
use Utils\HttpResponses;
use Utils\TimeControlServices;

class LoginController{

    function activeUsersPerDay(){
        $rnLogin = new RnLogin();
        TimeControlServices::startReq();

        //logica
        $body = $rnLogin->filtrarLoginPorData();

        TimeControlServices::finalReq();
        $requisition = [
            'Time stamp' => TimeControlServices::timeStamp(),
            'Tempo de processamento' => TimeControlServices::processTime(),
            'body' => $body
        ];

        if(!empty($body)){
            HttpResponses::ok($requisition);
            exit;
        } else {
            HttpResponses::notFound("Não existem dados para serem processados.");
            exit;
        }
    }

}

?>