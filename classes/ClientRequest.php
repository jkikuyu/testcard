<?php

namespace IpaySecure;
require_once('vendor/autoload.php');
require_once('classes/Utils.php');
/**
** @author :jude
**/

class ClientRequest{
	private $referenceID;
	private $accountNumber;
	private $expirationMonth;
	private $expirationYear ;
	private $currency ;
	private $amount;
	private $cardType;
	private $merchantId;
	private $transactionkey;
	private $request;
	function __construct(){
		$tag = 'ipaysecure';
		Utils::getLogFile($tag);

		$this->merchantId = getenv('MERCHANT_ID');
		$this->transactionkey = getenv('TRANSACTION_KEY');
		$this->paymentSolution=getenv('PAYMENTSOLUTION');

	}
	 public function payerAuthEnrollService($cardDetails){
		 	  
		$this->request = array();

		$this->request['referenceID'] = $cardDetails->referenceId;
		$this->request['payerAuthEnrollService_run'] = 'true';

		$this->request['card_expirationMonth'] = $cardDetails->Account->ExpirationMonth;
		$this->request['card_expirationYear'] = $cardDetails->Account->ExpirationYear;
		$this->request['card_cardType']=  $cardDetails->cardType;
		$this->request['card_accountNumber'] = $cardDetails->Account->AccountNumber;


		$res = self::makeRequest($cardDetails);
		return $res;
	}

	public function payerAuthValidateService($cardDetails){
		$this->request = array();
		echo "begin validation .....";
		//$this->request['payerAuthEnrollService_run'] = 'true';

		$this->request['payerAuthValidateService_authenticationTransactionID'] = $cardDetails->Payment->ProcessorTransactionId;
		$this->request['ccAuthService_run'] = 'true';
		$this->request['ccCaptureService_run'] = 'true';
		$this->request['payerAuthValidateService_run'] = 'true';
	//	$this->request['afsService_run'] = 'true';
	//	$this->request['ccAuthService_xid'] = $cardDetails->xid;
		//$this->request['ccAuthService_cavv'] = $ardDetails->cavv;
		$this->request['vc_orderID'] = $cardDetails->OrderDetails->OrderNumber;

		$this->request['card_expirationMonth'] = $cardDetails->Account->ExpirationMonth;
		$this->request['card_expirationYear'] = $cardDetails->Account->ExpirationYear;
		$this->request['card_cardType']=  $cardDetails->cardType;
		$this->request['card_accountNumber'] = $cardDetails->Account->AccountNumber;
		$this->request['billTo_firstName'] = $cardDetails->Consumer->BillingAddress->FirstName;
		$this->request['billTo_lastName']  = $cardDetails->Consumer->BillingAddress->LastName;
		$this->request['billTo_email'] = $cardDetails->Consumer->Email1;
		$this->request['billTo_street1']   = $cardDetails->Consumer->BillingAddress->Address1;
		$this->request['billTo_country'] = $cardDetails->Consumer->BillingAddress->CountryCode;

		$this->request['billTo_city'] 	=  $cardDetails->Consumer->BillingAddress->City;
		if($cardDetails->cardType=="002"){
			$this->request['card_cardType'] = $cardDetails->cardType;
		}


		$res = self::makeRequest($cardDetails);


		return $res;
	}
	public function advanceFraudScreenService($cardDetails){
		$this->request['afsService_run'] = 'true';

		$this->request['card_expirationMonth'] = $cardDetails->Account->ExpirationMonth;
		$this->request['card_expirationYear'] = $cardDetails->Account->ExpirationYear;
		$this->request['card_cardType']=  $cardDetails->cardType;
		$this->request['card_accountNumber'] = $cardDetails->Account->AccountNumber;
		$this->request['shipTo_firstName'] = $cardDetails->Consumer->BillingAddress->FirstName;
		$this->request['shipTo_lastName']  = $cardDetails->Consumer->BillingAddress->LastName;
		$this->request['shipTo_email'] = $cardDetails->Consumer->Email1;
		$this->request['shipTo_street1']   = $cardDetails->Consumer->BillingAddress->Address1;
		$this->request['shipTo_country'] = $cardDetails->Consumer->BillingAddress->CountryCode;

		$this->request['shipTo_city'] 	=  $cardDetails->Consumer->BillingAddress->City;
		$res = self::makeRequest($cardDetails);

	return $res;

	}
	public function authorizeOnline($cardDetails){
		$this->request = array();
		$this->request['paymentSolution']=$this->paymentSolution;
		$this->request['vc_orderID'] = $cardDetails->OrderDetails->OrderNumber;
		$this->request['ccAuthService_run'] = 'true';
		$res = self::makeRequest($cardDetails);

	return $res;
	}

	public function makeRequest($cardDetails){
		self::getCurrency($cardDetails);

		$options = [$this->merchantId,$this->transactionkey];

		$this->request['purchaseTotals_grandTotalAmount']=$cardDetails->OrderDetails->Amount/100;
		$this->request['purchaseTotals_currency'] =$cardDetails->OrderDetails->CurrencyCode;
		$this->request['merchantID'] = $this->merchantId;
		$this->request['merchantReferenceCode'] = $cardDetails->OrderDetails->OrderNumber;		
		$client = new \CybsNameValuePairClient($options);
		 $jsonStr= json_encode($cardDetails);
		Utils::infoMsg($jsonStr);
		Utils::infoMsg("\n---------------------------------------------------------\n");

		$res = $client->runTransaction($this->request);
		$info = $client->__getLastRequest();
		Utils::infoMsg($info);
		Utils::infoMsg("response :". $res);
		Utils::infoMsg("\n---------------------------------------------------------\n");

 
		return $res;

	}
	private function getCurrency($cardDetails){
		$arr =include('classes/iso_4217_currency_codes.php');

		foreach ($arr as $currency => $code) {
			 if ($code[1] ===$cardDetails->OrderDetails->CurrencyCode){
			 	$this->currency =$currency;
			 	break;
			}
		}

	}

}
?>
