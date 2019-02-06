<?php
namespace IpaySecure;
use Dotenv\Dotenv;   
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

final class Utils{
    private static $log;
    public static function validatePhpInput($raw_input, array $required_params){
        $res_arr = null;
        $res_obj = null;
        if($raw_input){
            foreach($required_params as $param){
                if(!property_exists($raw_input, $param) || empty($raw_input->$param) || !(is_string($raw_input->$param) || is_int($raw_input->$param))){
                    die($param . ' is required');
                }
                else{
                    $res_arr[$param] = $raw_input->$param;
                }
            }
            $res_obj = (object) $res_arr;
        }
        else{
            throw new Exception('The following parameters are required ' . json_encode($required_params));
        }
        //var_dump($res_obj);
    return $res_obj;
    }
    public static function formatError(\Exception $e, $error_desc){
        $message = ((json_decode($e->getMessage()) == null) ? $e->getMessage() : json_decode($e->getMessage()));
        $err_obj = (object) ['Desc' => $error_desc,
            'Line' => $e->getLine(),
            'File' => basename($e->getFile()),
            'Message' => $message,
            'StackTrace' => $e->getTraceAsString()
        ];
        return json_encode($err_obj);
    }
    public static function array_to_xml($array, &$xml_user_info) {
        foreach($array as $key => $value) {
            if(is_array($value)) {
                if(!is_numeric($key)){
                    $subnode = $xml_user_info->addChild("$key");
                    self::array_to_xml($value, $subnode);
                }else{
                    $subnode = $xml_user_info->addChild("item$key");
                    self::array_to_xml($value, $subnode);
                }
            }else {
                $xml_user_info->addChild("$key",htmlspecialchars("$value"));
            }
        }
    }
    public static function logger(array $logs){
        //this line can be replaced with typehint of string if php>=7.1
        $logs = json_encode($logs);

        $filepath = Utils::getLogFile();
        file_put_contents($filepath, $logs."\n", FILE_APPEND | LOCK_EX);
    }
    public static function getLogFile($tag="ipaySecure"){
        $tagname = $tag;
        $dotenv = new Dotenv(__DIR__.'/secure');
        $dotenv->load();
        $dirname = getenv('LOGDIR');

       // echo "dir: ". $dirname;
        
        if(!is_string($dirname)){
            throw new \InvalidArgumentException('dirname must be a string');
        }
        else{
            // $logs    = (is_array($logs))? json_encode($logs, JSON_PRETTY_PRINT): (string)$logs;
            
            $dir = $dirname;

            $base_dir = dirname(__dir__).'/';

            $save_dir = $base_dir.$dirname;

            $dir_exists = (file_exists($save_dir) && is_dir($save_dir));

            if(!$dir_exists){
                if(!mkdir($save_dir, 0755, true)){
                    throw new \Exception('Unable to create directory');
                }
            }
            $dir = $save_dir;
            $logFile = $dir."/".date("Y-m-d").'.log';
            self::$log = new Logger($tag);
            self::$log->pushHandler(new StreamHandler($logFile , Logger::INFO));

        }
    }
    public static function suddenDeath(){
        /**
        @author Jude
        date:19/10/2018
        This will cater for sudden program termination that is non recoverabel resulting from making non existent calls. 
        **/

        $filepath = Utils::getLogFile();

        $error = error_get_last();
        if ($error['type'] === E_ERROR) {
            // fatal error has occured
             $logs = json_encode($error);
            file_put_contents($filepath, $logs."\n", FILE_APPEND | LOCK_EX);

        }

    }
    public static function infoMsg($info){
        self::$log->info($info);

    }
    public static function errMsg($error){

        self::$log->Error($error);

    }

}
?>