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

    function topCountries(){
        $rnUsuarios = new RnUsuarios();
        TimeControlServices::startReq();
        
        $body = [
            'Quantidade por pais' =>$rnUsuarios->quantidadeSuperUsuariosPorPais(),
            'Usuarios agrupados por pais' => $rnUsuarios->agruparUsuariosPorPais()
        ];

        TimeControlServices::finalReq();
        
        $requisition = [
            'Time stamp: ' =>TimeControlServices::timeStamp(),
            'Tempo de processamento: ' => TimeControlServices::processTime(),
            'Body: ' => $body
        ];

        HttpResponses::ok($requisition);
    }

    function teamInsights(){
        $rnUsuarios = new RnUsuarios();
        TimeControlServices::startReq();

        $body = [
            'Usuarios por equipe' => $rnUsuarios->agruparPorEquipe()
        ];

        TimeControlServices::finalReq();

        $requisition = [
            'Time stamp' => TimeControlServices::timeStamp(),
            'Tempo de processamento: ' => TimeControlServices::processTime(),
            'Body: ' => $body
        ];

        if(!empty($body)){
            HttpResponses::ok($requisition);
        } else {
            HttpResponses::notFound("Não existem dados para serem analisados.");
        }      
        
    }

}

?>