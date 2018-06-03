<?php

include_once __DIR__ . '/abstraction.php';

class UserLocation extends Record{
    
    const TABLE = 'user_locations';
    
    public $user;
    public $ip;
    public $lat;
    public $lon;
    public $mac;
    public $created_date;
    
    public function __construct($UID = null){
        parent::__construct(self::TABLE,$UID);
    }    
}