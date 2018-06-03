<?php

require __DIR__ . '/abstraction.php';
require __DIR__ . '/UserAgentParser.php';

//$userAgent = $_SERVER['HTTP_USER_AGENT'] . "\n";
//file_put_contents('/tmp/access.log',$userAgent,FILE_APPEND);

class Request extends Record{

    const DRIVER = 'mssql';
    const DB = 'Sandbox';
    const TABLE = 'tbl_web_access';

    public $id;
    public $ip_address;
    public $platform;
    public $browser;
    public $version;
    public $responseCode;
    public $requestDate;
    public $requestMethod;
    public $query;
    public $endpoint;

    public function __construct($id = null)
    {
        parent::__construct(self::DRIVER,self::DB,self::TABLE,$id);
    }
    public static function lastRequest(){
        $results = $GLOBALS['db']
            ->driver(self::DRIVER)
            ->database(self::DB)
            ->table(self::TABLE)
            ->select("id")
            ->orderBy("id desc")
            ->get("value");
        return new self($results);
    }
}
class AccessLogParser{

    const IPPATTERN = '/[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}/';
    const DATEPATTERN = '/\[(.*)\-[0][6]/';
    const METHODPATTERN = '/"([A-Z]{3,6})\s\//';
    const QUERYPATTERN = '/\"[A-Z]{3,6}\s(.*)HTTP/';
    const ENDPOINTPATTERN = '/"http:(.*)\"\w/';
    const RESPONSEPATTERN = '/HTTP\/1\.1"\s([0-9]{3})/';

    public function __construct(){}

    public static function parseIP($logStr){
        if(preg_match(self::IPPATTERN,$logStr,$matches)){
            return trim($matches[0]);
        }
        return false;
    }
    public static function parseDate($logStr){
        if(preg_match(self::DATEPATTERN,$logStr,$matches)){
            $dateStr = trim($matches[1]);
            $datePieces = explode('/',$dateStr);
            $timePieces = explode(':',$datePieces[2]);
            $day = $datePieces[0];
            $month = date('m',strtotime($datePieces[1]));
            $year = $timePieces[0];
            $hour = $timePieces[1];
            $minute = $timePieces[2];
            $second = $timePieces[3];
            $dateStr = $month . '/' . $day . '/' . $year . ' ' . $hour . ':' . $minute . ':' . $second;
            return date('m/d/Y H:i:s',strtotime($dateStr));
        }
        return false;
    }
    public static function parseMethod($logStr){
        if(preg_match(self::METHODPATTERN,$logStr,$matches)){
            return trim($matches[1]);
        }
        return false;
    }
    public static function parseQuery($logStr){
        if(preg_match(self::QUERYPATTERN,$logStr,$matches)){
            return trim($matches[1]);
        }
        return false;
    }
    public static function parseEndPoint($logStr){
        if(preg_match(self::ENDPOINTPATTERN,$logStr,$matches)){
            return "http:" . preg_replace("/\"/",'',$matches[1]);
        }
        return false;
    }
    public static function parseResponseCode($logStr){
        if(preg_match(self::RESPONSEPATTERN,$logStr,$matches)){
            return trim($matches[1]);
        }
        return false;
    }
}


$last = Request::lastRequest();
$file = __DIR__ . '/access_log';
$data = file($file);
$counter = 0;
foreach($data as $line){
    $user = parse_user_agent($line);
    $request = new Request();
    $request->platform = $user['platform'];
    $request->browser = $user['browser'];
    $request->version = $user['version'];
    $request->ip_address = AccessLogParser::parseIP($line);
    $request->requestDate = AccessLogParser::parseDate($line);
    $request->requestMethod = AccessLogParser::parseMethod($line);
    $request->query = AccessLogParser::parseQuery($line);
    $request->endpoint = AccessLogParser::parseEndPoint($line);
    $request->responseCode = AccessLogParser::parseResponseCode($line);
    if(strtotime($request->requestDate) > strtotime($last->requestDate)){
        $request->create();
        $counter++;
    }
}
echo $counter . " requests processed\n";