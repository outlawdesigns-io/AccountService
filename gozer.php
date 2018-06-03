<?php
include_once __DIR__ . '/strUtils.php';
include_once __DIR__ . '/abstraction.php';
include_once __DIR__ . '/user.php';


class Gozer{
    const USERS = 'users';
    const IPAPI = 'http://ip-api.com/json/';
    const WHOISTMP = '/tmp/whois';

    public function __construct()
    {
    }
    public static function generateSecret(){
        $util = new StrUtilities();
        $str = '';
        for($i = 0; $i < 3; $i++){
            $word = $util->getRandomWord();
            $str .= $word;
        }
        return md5($str);
    }
    public function verifyUsername($username){
        $results = $GLOBALS['db']
                ->database(self::USERS)
                ->table(self::USERS)
                ->select("UID")
                ->where("username","=","'$username'")
                ->get();
        if(!mysqli_num_rows($results)){
            return false;
        }else{
            while($row = mysqli_fetch_assoc($results)){
                $id = $row['UID'];
            }
        }
        return $id;
    }
    public function iterateAttempts($username){
        $id = $this->verifyUsername($username);
        $user = new User($id);
        if($user->login_attempts++ >= 5){
            $user->update()->lockout();
        }else{
            $user->update();
        }
        return $this;
    }
    public function authenticate($username,$password){
        $id = $this->verifyUsername($username);
        if(!$id){
            throw new Exception('Invalid Username');
        }else{
            $user = new User($id);
            $hash = md5($password);
            if($user->password == $hash){
                return $user;
            }
        }
        return false;
    }
    public static function getIpData($ip){
        return json_decode(file_get_contents(self::IPAPI . $ip));
    }
    public function whois($query){
        $cmd = "whois " . escapeshellarg($query) . " > " . self::WHOISTMP;
        $output = shell_exec($cmd);
        if($output){
            throw new Exception($output);
        }
        return $this;
    }
    public static function isLocalRequest($remoteIp){
        $localIp = $_SERVER['SERVER_ADDR'];
        if($remoteIp == '127.0.0.1'){
            return true;
        }
        $localPieces = explode('.',$localIp);
        $remotePieces = explode('.',$remoteIp);
        if($localPieces[0] == $remotePieces[0] && $localPieces[1] == $remotePieces[1] && $localPieces[2] == $remotePieces[2]){
            return true;
        }
        return false;
    }
    public static function verifyToken($token){
        $results = $GLOBALS['db']
                ->database(self::USERS)
                ->table(self::USERS)
                ->select("UID")
                ->where("auth_token","=","'$token'")
                ->get();
        if(!mysqli_num_rows($results)){
            return false;
        }else{
            while($row = mysqli_fetch_assoc($results)){
                $id = $row['UID'];
            }
            $user = new User($id);
            if($user->isTokenExpired()){
                throw new Exception('Token Expired');
            }
        }
        return $user;
    }
    public static function parseUserAgentData($userAgentStr){
        return parse_user_agent($userAgentStr);
    }
}
