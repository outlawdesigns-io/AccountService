<?php

require_once __DIR__ . '/../Libs/Record/Record.php';

class UserLocation extends Record{

    const DB = 'users';
    const TABLE = 'user_locations';
    const PRIMARYKEY = 'UID';

    public $UID;
    public $user;
    public $ip;
    public $lat;
    public $lon;
    public $mac;
    public $created_date;

    public function __construct($UID = null){
        parent::__construct(self::DB,self::TABLE,self::PRIMARYKEY,$UID);
    }
}
