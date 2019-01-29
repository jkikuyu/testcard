<?php
    /**
    ** @author:andrew
    ** modified by Jude
    **/
namespace IpaySecure;

use Dotenv\Dotenv;   

final class Utils{
    public static function validatePhpInput($raw_input, array $required_params){
        $res = null;
        //var_dump($raw_input);
        $isValid = true;
        if($raw_input){
            foreach($required_params as $param){
                if(!property_exists($raw_input, $param) || empty($raw_input->$param) || !(is_string($raw_input->$param) || is_int($raw_input->$param))){
                    //die($param . ' is required');
                    $_SESSION['res']= '{"errorcode":"1",
                        "message":$param . " is required"
                    }';
                    $isValid = false;
                    break;
                }
                else{
                    if($param == 'email'){
                        $email = $raw_input->$param;
                        $isValid = (new Utils())->validEmail($email);
                        if(!$isValid){
                            $_SESSION['res']= '{"errorcode":"2",
                                "message":$param . " is invalid"
                                }';

                            $isValid = false;
                            break;
                        }
                    }
                    //$res_obj[$param] = $raw_input->$param;
                }
            }
        }
        else{
            $isValid = false;
            $json = json_encode($required_params);
            $_SESSION['res'] = $json;
            
            throw new Exception('The following parameters are required ' . $json);
        }
        return $isValid;

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
    public static function getLogFile(){
        $dotenv = new Dotenv(__DIR__.'/secure');
        $dotenv->load();
        $dirname = getenv('LOGDIR');

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

        }
        return  $dir."/".date("Y-m-d").'.log';
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
    public static function logger(array $logs, $dirname){
        //this line can be replaced with typehint of string if php>=7.1
        if(!is_string($dirname)){
            throw new \InvalidArgumentException('dirname must be a string');
        }
        else{
            // $logs	= (is_array($logs))? json_encode($logs, JSON_PRETTY_PRINT): (string)$logs;
            $logs = json_encode($logs);
            
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

            file_put_contents($dir."/".date("Y-m-d").'.log', $logs."\n", FILE_APPEND | LOCK_EX);
        }
    }
    private function validEmail($email){
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        $isValid = false;
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
             $isValid  = true;
        } 
        return  $isValid;
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