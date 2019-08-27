<?php

class StrUtilities{

    const RANDOMWORDAPI = 'http://api.outlawdesigns.io:9600';
    public function __construct()
    {
    }
    public static function getRandomWord(){
        return file_get_contents(self::RANDOMWORDAPI);
    }
}
