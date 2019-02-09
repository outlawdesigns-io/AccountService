<?php

require_once __DIR__ . '/api.php';
require_once __DIR__ . '/gozer.php';

class SecureEndPoint extends API{

    protected $user;
    protected $tokenData;


    public function __construct($request,$origin){
        parent::__construct($request);
        if(isset($this->headers['request_token']) && $this->endpoint == "authenticate"){
            $this->processTokenRequest();
        }elseif(!isset($this->headers['auth_token'])){
            throw new Exception('Access Denied. Token not present.');
        }else{
            try{
                $this->user = GOZER::verifyToken($this->headers['auth_token']);
                if(!$this->user){
                    throw new Exception('Access Denied. Invalid Token');
                }
            }catch(Exception $e){
                $str = $e->getMessage();
                throw new Exception($str);
            }
        }
    }
    private function processTokenRequest(){
        if(!isset($this->headers['password'])){
            throw new Exception('Missing credentials');
        }else{
            $gozer = new Gozer();
            try{
                $user = $gozer->authenticate($this->headers['request_token'],$this->headers['password']);
                if(!$user){
                    $gozer->iterateAttempts($this->headers['request_token']);
                    throw new Exception("Invalid Credentials. Event Logged.");
                }elseif($user->lock_out){
                    throw new Exception("Account Locked");
                }else{
                  if(!Gozer::isLocalRequest($_SERVER['REMOTE_ADDR'])){
                    $user->ip_address = $_SERVER['REMOTE_ADDR'];
                    $user->updateLocation();
                  }
                  if($user->isTokenExpired()){
                    $user->createToken();
                  }
                  $this->tokenData = array("token"=>$user->auth_token,"secret"=>$gozer->generateSecret());
                }
            }catch(Exception $e){
                $str = $e->getMessage();
                throw new Exception($str);
            }
        }
        return $this;
    }
}
