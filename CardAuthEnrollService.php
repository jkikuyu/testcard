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
	if ($combined['payerAuthEnrollReply_reasonCode']==="475"){ 
		$json = json_encode($combined);
		echo $json;
	}
	else{
		$json = json_encode($combined);
		echo $json;
 
		$req->authorizeOnline($recd_data);
		echo $json;
	}

}

/*session_unset();
session_destroy();*/


?>