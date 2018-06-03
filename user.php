<?php

include_once __DIR__ . '/abstraction.php';
include_once __DIR__ . '/gozer.php';
include_once __DIR__ . '/userLocation.php';

class User extends Record{
    
    const TABLE = 'users';
    
    public $username;
    public $password;
    public $auth_token;
    public $token_expiration;
    public $secret;
    public $ip_address;
    public $mac_address;
    public $lat;
    public $lon;
    public $created_date;
    public $created_by;
    public $updated_date;
    public $updated_by;
    public $last_login;
    public $status_id;
    public $first_name;
    public $last_name;
    public $dob;
    public $street_address;
    public $city;
    public $state;
    public $email;
    public $phone;
    public $domain;
    public $login_attempts;
    public $lock_out;
    public $lock_out_expiration;
    
    public function __construct($UID = null){
        parent::__construct(self::TABLE,$UID);
    }
    public function lockout($expiration = null){
        if(is_null($expiration)){
            $expiration = date('Y.m.d H:i:s',strtotime('next friday'));
        }
        $this->lock_out = 1;
        $this->lock_out_expiration = $expiration;
        $this->update();
        return $this;
    }
    public function isTokenExpired(){
        if(strtotime($this->token_expiration) <= time()){
            return true;
        }
        return false;
    }
    public function expireToken(){
        $this->token_expiration = date('Y.m.d H:i:s');
        $this->update();
        return $this;
    }
    public function createToken(){
        $token = array(
            "ip"=>$this->ip_address,
            "username"=>$this->username,
            "lat"=>$this->lat,
            "long"=>$this->lon,
            "salt"=>mt_rand(0,999)
        );
        $this->auth_token = JWT::encode($token,$this->secret);
        $this->token_expiration = date("Y.m.d H:i:s",strtotime("4 hours"));
        $this->update();
        return $this;
    }
    public function createSecret(){
        $this->secret = Gozer::generateSecret();
        $this->update();
        return $this;
    }
    public function updateLocation(){
        $ipInfo = Gozer::getIpData($this->ip_address);
        $this->lon = $ipInfo->lon;
        $this->lat = $ipInfo->lat;
        $location = new UserLocation();
        $location->user = $this->username;
        $location->ip = $this->ip_address;
        $location->lat = $this->lat;
        $location->lon = $this->lon;
        $location->create();
        return $this;
    }
}