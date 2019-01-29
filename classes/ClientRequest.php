<?php
/**
** @author :jude
**/

namespace IpaySecure;
	require_once('vendor/autoload.php');

	class ClientRequest{
	private $referenceID;
	private $firstName ;
	private $lastName;
	private $street ;
	private $city;
	private $email;
	private $accountNumber;
	private $expirationMonth;
	private $expirationYear ;
	private $currency ;
	private $amount;
	private $cardType;
	private $merchantId;
	private $transactionkey;

	function __construct(){
		$tag = 'ipaysecure';
		Utils::getLogFile($tag);

		$this->merchantId = getenv('MERCHANT_ID');
		$this->transactionkey = getenv('TRANSACTION_KEY');

	}
	 public function payerAuthEnrollService($cardDetails){
		 	  
		$request = array();

		$request['referenceID'] = $cardDetails->referenceID;
		$request['payerAuthEnrollService_run'] = 'true';
		$res = makeRequest($cardDetails);
		return $res;
	}

	public function validateEnrollDetails($cardDetails){
		$request = array();
		$request['payerAuthValidateService_authenticationTransactionID'] = this.transactionId;
		$request['payerAuthValidateSexrvice_run'] = 'true';
		$res = makeRequest($cardDetails);
		return $res;
	}
	
	public function makeRequest($cardDetails){
		self::getCurrency();

		$options = [$this->merchantId,$this->transactionkey];

		$request['purchaseTotals_grandTotalAmount']=$cardDetails->OrderDetails->Amount/10
		$request['card_accountNumber'] = $cardDetails->accountNumber;
		$request['card_expirationMonth'] = $cardDetails->Account->ExpirationMonth;
		$request['card_expirationYear'] = $cardDetails->Account->ExpirationYear;
		$request['purchaseTotals_currency'] =$this->currency;
		$request['card_cardType']=  $cardDetails->cardType;
		$request['merchantID'] = $this->merchantId;
		$request['merchantReferenceCode'] = $cardDetails->OrderDetails->OrderNumber;		

		$client = new \CybsNameValuePairClient($options);
		$res = $client->runTransaction($request);
		return $res;

	}
	private function getCurrency(){
		$arr =include('classes/iso_4217_currency_codes.php');

		foreach ($arr as $currency => $code) {
			 if ($code[1] === $cardDetails->OrderDetails->CurrencyCode){
			 	$this->currency =$currency;
			 	break;
			}
		}

	}

	function validCard($required){
		foreach ($required as $key => $value) {
		if (empty($value)) {
                throw new Exception(strtolower(str_replace('_',' ',$key)) . ' is missing and is a required.');
            }
            else{
            	$this->$key = $value;
            }

        }
	}
}
?>
