<?php

require_once __DIR__ . '/SecureEndPoint.php';
require_once __DIR__ . '/user.php';

class EndPoint extends SecureEndPoint{


    public function __construct($request,$origin,$remoteHost)
    {
        parent::__construct($request);
    }
    protected function example(){
        return array("endPoint"=>$this->endpoint,"verb"=>$this->verb,"args"=>$this->args,"request"=>$this->request);
    }
    protected function authenticate(){
        return $this->tokenData;
    }
    protected function user(){
        $data = null;
        if(!isset($this->verb) && !isset($this->args[0]) && $this->method == 'POST'){ //create
            $data = new User();
            $data->setFields($this->request)->create();
        }elseif(!isset($this->verb) && !isset($this->args[0]) && $this->method == 'GET'){ //get all
            
        }elseif(!isset($this->verb) &&(int)$this->args[0] && $this->method == 'GET'){ //get a movie by id
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
            
        }elseif(!isset($this->verb) &&(int)$this->args[0] && $this->method == 'GET'){ //get a movie by id
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
