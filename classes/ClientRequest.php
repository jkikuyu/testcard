<?php
/**
** @author :jude
**/

namespace IpaySecure;
	require_once('vendor/autoload.php');

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
		$this->request['payerAuthValidateService_authenticationTransactionID'] = $cardDetails->Payment->ProcessorTransactionId;
		$this->request['payerAuthValidateService_run'] = 'true';
		$this->request['card_expirationMonth'] = $cardDetails->Account->ExpirationMonth;
		$this->request['card_expirationYear'] = $cardDetails->Account->ExpirationYear;
		$this->request['card_cardType']=  $cardDetails->cardType;
		$this->request['card_accountNumber'] = $cardDetails->Account->AccountNumber;


		$res = self::makeRequest($cardDetails);
		return $res;
	}
	public function authorizeOnline($cardDetails){
		$this->request = array();
		$this->request['ccAuthService_run'] = 'true';
		$this->request['paymentSolution']=$this->paymentSolution;
		$this->request['vc_orderID'] = $cardDetails->OrderDetails->OrderNumber;
		$res = self::makeRequest($cardDetails);

	return $res;
	}

	public function makeRequest($cardDetails){
		self::getCurrency($cardDetails);

		$options = [$this->merchantId,$this->transactionkey];

		$this->request['purchaseTotals_grandTotalAmount']=$cardDetails->OrderDetails->Amount/100;
		$this->request['purchaseTotals_currency'] =$this->currency;
		$this->request['merchantID'] = $this->merchantId;
		$this->request['merchantReferenceCode'] = $cardDetails->OrderDetails->OrderNumber;		
		$client = new \CybsNameValuePairClient($options);
		$res = $client->runTransaction($this->request);
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
