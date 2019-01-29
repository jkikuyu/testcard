<?php
$url = "centineltest.cardinalcommerce.com";
$headers = ["Content-type:application/x-www-form-urlencoded;charset=UTF-8"];

$data = '<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="urn:schemas-cybersource-com:transaction-data-1.151" xmlns:ns2="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd"><SOAP-ENV:Header><ns2:Security SOAP-ENV:mustUnderstand="1"><ns2:UsernameToken><ns2:Username>ipayafrica_61007001_kshs</ns2:Username><ns2:Password>UfLYBw9cb+tMAXDY1jrJhvYXZX5re4qn/9k1qiUdqTSQ55tXCM7QvrNR6ZbvC10IIvJWaW2jxmJS9mFF7Pnsg4kRfkPrmZAQ/liDOgvT0ToHy9qh0o1mG7pJeCg5ZT/KhJCQmVapvii7NTVHukrKO+AsTxPULNH79TisD+t3h0hNN2jQHXiBzdTdL6z5ohKV3PWtDAOJXvVb7qMsUHWhfMpWZiokk927ZBP+S4PNRyhFAQyZJjLVaBrovt+MS4woC8MXE5/HHWw+6/3O1azkmKhIfsYCa+JvNdOTQ4fA9fb0UoX/5arrg++SUNsCrRUnC8bhl5DCj07zBjyyX/Q0yQ==</ns2:Password></ns2:UsernameToken></ns2:Security></SOAP-ENV:Header><SOAP-ENV:Body><ns1:nvpRequest>referenceID=5c4ff0bcef883
purchaseTotals_currency=USD
purchaseTotals_grandTotalAmount=30000
card_accountNumber=400000
card_expirationMonth=12
card_expirationYear=2019
card_cardType=001
merchantID=ipayafrica_61007001_kshs
merchantReferenceCode=1234567890
payerAuthEnrollService_run=true
</ns1:nvpRequest></SOAP-ENV:Body></SOAP-ENV:Envelope>
';

//init curl
$curl = curl_init();

//build json string

/**
 * CURL OPTIONS
 */
//set url
curl_setopt($curl, CURLOPT_URL, $url);

//set request headers
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

//return transfer response as string to the $curl resource
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

//follow any 'Location:' header the server sends
curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

//output verbose info
curl_setopt($curl, CURLOPT_VERBOSE, true);

//request method is POST
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");

//request body
curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

$output = curl_exec($curl);

echo $output;

	
?>