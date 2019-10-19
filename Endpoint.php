<?php

require_once __DIR__ . '/Libs/Api/Api.php';
require_once __DIR__ . '/Models/User.php';
require_once __DIR__ . '/Gozer.php';

class EndPoint extends Api{

    const TOKN_HDR = 'auth_token';
    const AUTH_HDR = 'password';
    const REQ_HDR = 'request_token';

    protected static $_authErrors = array(
        'headers'=>'Missing required Headers',
        'noToken'=>'Access Denied. No Token Present',
        'badToken'=>'Access Denied. Invalid Token',
        'noPassword'=>'Missing credentials',
        'accountLocked'=>'Account Locked',
        'badCreds'=>'Invalid Credentials. Event Logged'
    );
    protected $_tokenData = array();


    public function __construct($request,$origin)
    {
        parent::__construct($request);
        if(isset($this->headers[self::REQ_HDR]) && $this->endpoint == "authenticate"){
          $this->_processTokenRequest();
        }elseif(isset($this->headers[self::REQ_HDR]) && !isset($this->headers[self::AUTH_HDR])){
            throw new \Exception(self::$_authErrors['headers']);
        }elseif(!isset($this->headers[self::TOKN_HDR]) && !isset($this->headers[self::REQ_HDR])){
            throw new \Exception(self::$_authErrors['noToken']);
        }elseif(!$this->_verifyToken() && !isset($this->headers[self::REQ_HDR])){
            throw new \Exception(self::$_authErrors['badToken']);
        }
    }
    protected function _processTokenRequest(){
        if(!isset($this->headers[self::AUTH_HDR])){
            throw new \Exception(self::$_authErrors['noPassword']);
        }
        try{
            if(!$user = User::authenticate($this->headers[self::REQ_HDR],$this->headers[self::AUTH_HDR])){
              throw new \Exception(self::$_authErrors['badCreds']);
            }elseif($user->lock_out){
              throw new \Exception(self::$_authErrors['accountLocked']);
            }else{
              // if(!Gozer::isLocalRequest($_SERVER['REMOTE_ADDR'])){}
              $user->ip_address = $_SERVER['REMOTE_ADDR'];
              $user->updateLocation();
              if($user->isTokenExpired()){
                $user->createToken();
              }
              $this->_tokenData = array("token"=>$user->auth_token,"secret"=>Gozer::generateSecret());
            }
        }catch(\Exception $e){
            throw new \Exception($e->getMessage());
        }
        return $this;
    }
    protected function _verifyToken(){
        if(!$user = User::verifyToken($this->headers[self::TOKN_HDR])){
          throw new \Exception(self::$_authErrors['badToken']);
        }
        return $user;
    }
    protected function authenticate(){
        return $this->_tokenData;
    }
    protected function verify(){
      try{
        return $this->_verifyToken();
      }catch(\Exception $e){
        throw new \Exception($e->getMessage());
      }
    }
    protected function user(){
        $data = null;
        if(!isset($this->verb) && !isset($this->args[0]) && $this->method == 'POST'){ //create
            $data = new User();
            $data->setFields($this->request)->create();
        }elseif(!isset($this->verb) && !isset($this->args[0]) && $this->method == 'GET'){ //get all

        }elseif(!isset($this->verb) &&(int)$this->args[0] && $this->method == 'GET'){ //get by id
            $data = new User($this->args[0]);
        }elseif((int)$this->args[0] && $this->method == 'PUT'){ //update by id
            $data = new User($this->args[0]);
            $data->setFields($this->file)->update();
        }elseif(isset($this->verb)){
            $data = $this->_parseVerb();
        }else{
            throw new \Exception('Malformed Request');
        }
        return $data;
    }
    protected function location(){
        $data = null;
        if(!isset($this->verb) && !isset($this->args[0]) && $this->method == 'POST'){ //create
            $data = new UserLocation();
            $data->setFields($this->request)->create();
        }elseif(!isset($this->verb) && !isset($this->args[0]) && $this->method == 'GET'){ //get all

        }elseif(!isset($this->verb) &&(int)$this->args[0] && $this->method == 'GET'){ //get by id
            $data = new UserLocation($this->args[0]);
        }elseif((int)$this->args[0] && $this->method == 'PUT'){ //update by id
            $data = new UserLocation($this->args[0]);
            $data->setFields($this->file)->update();
        }elseif(isset($this->verb)){
            $data = $this->_parseVerb();
        }else{
            throw new \Exception('Malformed Request');
        }
        return $data;
    }
}
