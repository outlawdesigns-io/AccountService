<?php

require_once __DIR__ . '/../Libs/Record/Record.php';

class PasswordAttempt extends Record{

    const DB = 'users';
    const TABLE = 'PasswordAttempt';
    const PRIMARYKEY = 'UID';

    public $UID;
    public $userId;
    public $password;
    public $created_date;

    public function __construct($UID = null){
        parent::__construct(self::DB,self::TABLE,self::PRIMARYKEY,$UID);
    }
}
