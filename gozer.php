<?php
require_once __DIR__ . '/Libs/StrUtils.php';
require_once __DIR__ . '/Models/User.php';


class Gozer{

    const LOOPBACK = '127.0.0.1';
    const IPAPI = 'http://ip-api.com/json/';

    public function __construct()
    {
    }
    public static function generateSecret(){
        $str = '';
        for($i = 0; $i < 3; $i++){
            $word = StrUtilities::getRandomWord();
            $str .= $word;
        }
        return md5($str);
    }
    public static function getIpData($ip){
        return json_decode(file_get_contents(self::IPAPI . $ip));
    }
    public static function isLocalRequest($remoteIp){
        $localIp = $_SERVER['SERVER_ADDR'];
        if($remoteIp == self::LOOPBACK){
            return true;
        }
        $localPieces = explode('.',$localIp);
        $remotePieces = explode('.',$remoteIp);
        if($localPieces[0] == $remotePieces[0] && $localPieces[1] == $remotePieces[1] && $localPieces[2] == $remotePieces[2]){
            return true;
        }
        return false;
    }
}
