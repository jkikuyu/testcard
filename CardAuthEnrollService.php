<?php
namespace IpaySecure;
require_once ('classes/ClientRequest.php');
require_once ('classes/Utils.php');



error_reporting(E_ALL);
ini_set('display_errors', 1);
//session_start();

$jsonData = file_get_contents('php://input');
//echo $jsonData;
$req = new ClientRequest();
$json = "";
if(isset($jsonData)){
	//echo $jsonData;
	$recd_data = json_decode($jsonData);
	$res = $req->payerAuthEnrollService($recd_data);
	preg_match_all("/ ([^:=]+) [:=]+ ([^\\n]+) /x",  $res, $p);
	$keys = array_map('trim',$p[1]);
	$values = array_map('trim',$p[2]);
	$combined = array_combine($keys, $values);
    $json = json_encode($combined);

    switch($combined['payerAuthEnrollReply_reasonCode']){
            
        case "475": 
            $json = json_encode($combined);
            echo $json;
            break;
        case "100":
            $json = json_encode($combined);
            echo $json;

            $req->authorizeOnline($recd_data, $combined);
            echo $json;
            break;
        default:
            echo $json;

    
    }


}

/*session_unset();
session_destroy();*/


?>