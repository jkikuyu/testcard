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

if(isset($jsonData)){
	//echo $jsonData;
	$recd_data = json_decode($jsonData);
	$res = $req->payerAuthValidateService($recd_data);
	preg_match_all("/ ([^:=]+) [:=]+ ([^\\n]+) /x",  $res, $p);
	$keys = array_map('trim',$p[1]);
	$values = array_map('trim',$p[2]);
	$combined = array_combine($keys, $values);
	$json = json_encode($combined);
	
	//echo $json;
	$arr =include('classes/reason_codes.php');
	$pos = strpos($res, 'reasonCode');
	$pos +=11;
	$reasonCode = substr($res, $pos, 3);
	$mess = "Contact your bank";
	foreach ($arr as $code => $reason) {

		 if ($code==$reasonCode){
		 	$json = json_encode(array_merge(json_decode($json, true),["reason"=>$reason]));
		 	break;
		}
	}
	if($combined['payerAuthValidateReply_reasonCode'] ==="100"){
		$res = $req->advanceFraudScreenService($recd_data);
		preg_match_all("/ ([^:=]+) [:=]+ ([^\\n]+) /x",  $res, $p);
		$keys = array_map('trim',$p[1]);
		$values = array_map('trim',$p[2]);
		$combined = array_combine($keys, $values);
		$fjson = json_encode($combined);
		$json = json_encode(array_merge(json_decode($json, true),json_decode($fjson, true)));

	}


	/*if($combined['payerAuthValidateReply_reasonCode'] ==="100"){
		$res = $req->authorizeOnline($recd_data);
		$arr =include('classes/reason_codes.php');

		$reasonCode = substr($res, 11, 3);
		$mess = "Contact Bank";
		foreach ($arr as $code => $reason) {
			 if ($code==$reasonCode){
			 	$mess =$reason;
			 	break;
			}
		}

	}*/


	echo $json;
}

/*session_unset();
session_destroy();*/


?>