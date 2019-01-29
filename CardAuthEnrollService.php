<?php
namespace IpaySecure;
require_once ('classes/ClientRequest.php');
require_once ('classes/Utils.php');



error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

$jsonData = file_get_contents('php://input');
//echo $jsonData;
$req = new ClientRequest();

if(isset($jsonData)){
	//echo $jsonData;
	$recd_data = json_decode($jsonData);
	$res = $req->payerAuthEnrollService($recd_data);
	preg_match_all("/ ([^:=]+) [:=]+ ([^\\n]+) /x",  $res, $p);
	$arr = array_combine($p[1], $p[2]);
	print_r($arr);
	print_r($arr['payerAuthEnrollReply_reasonCode']);


/*	if ($arr[payerAuthEnrollReply_reasonCode]==475){
			$req->payerAuthValidateService($json);
	}
	else{

	}
*/
	$json = json_encode($arr);

	echo $json;
}

session_unset();
session_destroy();


?>