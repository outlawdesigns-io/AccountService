<?php

class AccessLog{

    const IPPATTERN = '/[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}/';
    const DATEPATTERN = '/\[(.*)\-[0][6]/';
    const METHODPATTERN = '/"([A-Z]{3,6})\s\//';
    const QUERYPATTERN = '/\"[A-Z]{3,6}\s(.*)HTTP/';
    const ENDPOINTPATTERN = '/"http:(.*)\"\w/';
    const RESPONSEPATTERN = '/HTTP\/1\.1"\s([0-9]{3})/';

    public function __construct(){}

    public static function parseIP($logStr){
        if(preg_match(self::IPPATTERN,$logStr,$matches)){
            return trim($matches[0]);
        }
        return false;
    }
    public static function parseDate($logStr){
        if(preg_match(self::DATEPATTERN,$logStr,$matches)){
            $dateStr = trim($matches[1]);
            $datePieces = explode('/',$dateStr);
            $timePieces = explode(':',$datePieces[2]);
            $day = $datePieces[0];
            $month = date('m',strtotime($datePieces[1]));
            $year = $timePieces[0];
            $hour = $timePieces[1];
            $minute = $timePieces[2];
            $second = $timePieces[3];
            $dateStr = $month . '/' . $day . '/' . $year . ' ' . $hour . ':' . $minute . ':' . $second;
            return date('m/d/Y H:i:s',strtotime($dateStr));
        }
        return false;
    }
    public static function parseMethod($logStr){
        if(preg_match(self::METHODPATTERN,$logStr,$matches)){
            return trim($matches[1]);
        }
        return false;
    }
    public static function parseQuery($logStr){
        if(preg_match(self::QUERYPATTERN,$logStr,$matches)){
            return trim($matches[1]);
        }
        return false;
    }
    public static function parseEndPoint($logStr){
        if(preg_match(self::ENDPOINTPATTERN,$logStr,$matches)){
            return "http:" . preg_replace("/\"/",'',$matches[1]);
        }
        return false;
    }
    public static function parseResponseCode($logStr){
        if(preg_match(self::RESPONSEPATTERN,$logStr,$matches)){
            return trim($matches[1]);
        }
        return false;
    }
}