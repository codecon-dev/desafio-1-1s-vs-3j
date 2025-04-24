<?php

namespace Utils;

require_once __DIR__ . '/../../vendor/autoload.php';

class JsonServices{

    private static $pastaJsons = __DIR__ . '/../../jsons/';

    public static function lerUltimoJson(){
        
        if(!is_dir(self::$pastaJsons)){
            return null;
        }

        $arquivos = glob(self::$pastaJsons . '*.json');

        if(!$arquivos){
            return null;
        }

        usort($arquivos, function($a, $b){
            return filemtime($b) - filemtime($a);
        });

        $ultimoArquivo = $arquivos[0];

        $conteudo = file_get_contents($ultimoArquivo);
        $json = json_decode($conteudo, true);

        if(json_last_error() !== JSON_ERROR_NONE){
            return null;
        }

        return $json;
    }
}

?>