<?php

require_once __DIR__ . '/../Libs/Record/Record.php';
require_once __DIR__ . '/../Libs/JWT.php';
require_once __DIR__ . '/../Gozer.php';
require_once __DIR__ . '/UserLocation.php';
require_once __DIR__ . '/PasswordAttempt.php';

class User extends Record{

    const DB = 'users';
    const TABLE = 'users';
    const PRIMARYKEY = 'UID';
    const TOKENLIFE = '4 hours';
    const LOCKOUT_ATTMPTS = '5';
    const DEF_LOCKOUT = 'next friday';

    public $UID;
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
        parent::__construct(self::DB,self::TABLE,self::PRIMARYKEY,$UID);
    }
    public function lockout($expiration = null){
        if(is_null($expiration)){
            $expiration = date('Y.m.d H:i:s',strtotime(self::DEF_LOCKOUT));
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
        $this->token_expiration = date("Y.m.d H:i:s",strtotime(self::TOKENLIFE));
        $this->update();
        return $this;
    }
    public function createSecret(){
        $this->secret = Gozer::generateSecret();
        $this->update();
        return $this;
    }
    public function updateLocation(){
        $location = new UserLocation();
        $location->user = $this->username;
        $location->ip = $this->ip_address;
        if(!Gozer::isLocalRequest($_SERVER['REMOTE_ADDR'])){
            $ipInfo = Gozer::getIpData($this->ip_address);
            $this->lon = $ipInfo->lon;
            $this->lat = $ipInfo->lat;
            $location->lat = $this->lat;
            $location->lon = $this->lon;
        }
        $location->create();
        return $this;
    }
    public static function verifyToken($token){
        $results = $GLOBALS['db']
            ->database(self::DB)
            ->table(self::TABLE)
            ->select(self::PRIMARYKEY)
            ->where("auth_token","=","'" . $token . "'")
            ->get();
        if(!mysqli_num_rows($results)){
            return false;
        }
        while($row = mysqli_fetch_assoc($results)){
          $id = $row[self::PRIMARYKEY];
        }
        $user = new self($id);
        if($user->isTokenExpired()){
          throw new \Exception('Token Expired');
        }
        return $user;
    }
    public static function verifyUsername($username){
        $results = $GLOBALS['db']
            ->database(self::DB)
            ->table(self::TABLE)
            ->select(self::PRIMARYKEY)
            ->where("username","=","'" . $username . "'")
            ->get();
        if(!mysqli_num_rows($results)){
            return false;
        }
        while($row = mysqli_fetch_assoc($results)){
            $id = $row[self::PRIMARYKEY];
        }
        return $id;
    }
    public static function activeSessions(){
      $data = array();
      $results = $GLOBALS['db']
          ->database(self::DB)
          ->table(self::TABLE)
          ->select(self::PRIMARYKEY)
          ->where("token_expiration",">","NOW()")
          ->get();
      while($row = mysqli_fetch_assoc($results)){
        $data[] = new self($row[self::PRIMARYKEY]);
      }
      return $data;
    }
    public static function iterateAttempts($username){
        if(!$id = self::verifyUsername($username)){
            return false;
        }
        $user = new User($id);
        if($user->login_attempts++ >= self::LOCKOUT_ATTMPTS){
            $user->update()->lockout();
        }else{
            $user->update();
        }
        return true;
    }
    public static function authenticate($username,$password){
        if(!$id = self::verifyUsername($username)){
            throw new \Exception('Invalid Username');
        }else{
            $user = new self($id);
            $hash = md5($password);
            if($user->password == $hash){
                return $user;
            }else{
              $attempt = new PasswordAttempt();
              $attempt->userId = $id;
              $attempt->password = $password;
              $attempt->create();
              self::iterateAttempts($username);
            }
        }
        return false;
    }
}
