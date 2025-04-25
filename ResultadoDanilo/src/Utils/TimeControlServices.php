<?php

namespace Utils;

use DateTime;

class TimeControlServices{

    private static float $startTime = 0;
    private static float $endTime = 0;

    public static function startReq(){
        if(self::$startTime == 0){
            self::$startTime = microtime(true);
        }        
    }

    public static function finalReq(){
        self::$endTime = microtime(true);
    }

    public static function processTime(){
        if(self::$startTime === 0 || self::$endTime === 0){
            return 0;
        }

        return (self::$endTime - self::$startTime)*1000;
    }

    public static function timeStamp(){
        if(self::$startTime){
            self::startReq();
        }

        return gmdate('Y-m-d\TH:i:s\Z', (int) self::$startTime);
    }





}

?>