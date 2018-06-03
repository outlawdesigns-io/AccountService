<?php

class StrUtilities{

    const RANDOMWORDAPI = 'http://setgetgo.com/randomword/get.php';

    public function __construct()
    {
    }
    public function getRandomWord(){
        return file_get_contents(self::RANDOMWORDAPI);
    }
}