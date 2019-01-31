<?php
	namespace IpaySecure;
	use IpaySecure\JWTUtil;

	//use IpaySecure\Utils;
	require_once('vendor/autoload.php');
	require_once('classes/JWTUtil.php');


	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	// log request
/*
	Utils::logger(array_merge(['request_time' => new \DateTime(), 'request_type' => 'php://input'], ['request_data' => json_decode(file_get_contents('php://input'))]), $request_log_dir);	
*/


?>
<html>
<head>

</head>
<body>
<?php
/**
  * thin client architecture: https://cardinaldocs.atlassian.net/wiki/spaces/CCen/pages/56229960/Getting+Started
**/
/* Test Data*/
	//use IpaySecure\Secure3d\ClientRequest;


	$jsonData = file_get_contents('php://input');
	$recd_data = '';
	//echo $jsonData;
	if(!isset($jsonData) || empty($jsonData)){
		//sample data
		$jsonData = '{
			"cardType":"001",
			"street":"Sifa Towers, Lenana Rd",
			"OrderDetails":{
				"OrderNumber":"1234567890",
				"OrderDescription":"test Description", 
				"Amount":"30000",
				"CurrencyCode":"840",
				"OrderChannel":"M",
				"TransactionId":"'.uniqid().'"
			},
			"Account":{
				"AccountNumber":"4000000000000002",
				"CardCode":"366",
				"ExpirationMonth":"12",
				"ExpirationYear":"2019"
			}


		}';
/*				"Consumer":{
				"Email1":"abc@test.com",
				"BillingAddress":{
					"FirstName":"John",
					"MiddleName":"C",
					"LastName":"Doe",
					"Address1":"sdfdfdfddfddf",
					"City":"Nairobi",
					"Phone1":"3234455"
				}
			},
*/
		}
	$recd_data = json_decode($jsonData);
	$referenceId = uniqid();
	$aref = ["referenceId"=>$referenceId];
	$jsonData = json_encode(array_merge(json_decode($jsonData,true),$aref));

	$jwtUtil = new JWTUtil();
	$jwt = $jwtUtil->generateJwt($recd_data->OrderDetails->TransactionId, $recd_data->OrderDetails, $referenceId);
?>
<!--https://cardinaldocs.atlassian.net/wiki/spaces/CC/pages/557065/Songbird.js#Songbird.js-Events -->
<!--Production URL: https://songbirdstag.cardinalcommerce.com/edge/v1/songbird.js -->
<!--Staging URL: https://songbirdstag.cardinalcommerce.com/edge/v1/songbird.js -->

<!--Sandbox URL: https://utilsbox.cardinalcommerce.com/cardinalcruise/v1/songbird.js -->
	<script src="https://songbirdstag.cardinalcommerce.com/cardinalcruise/v1/songbird.js"></script>

	<!--script src="https://includestest.ccdc02.com/cardinalcruise/v1/songbird.js"></script>-->
	<script src="https://code.jquery.com/jquery-3.3.0.js"></script>
	<script>
		var purchase = <?php echo $jsonData; ?>;
		//console.log(purchase);
		var enrollobj = "";

		var orderObject = {
		  Consumer: {
			Account: {
			  AccountNumber: purchase.Account.AccountNumber
			}
		  }
		};

		$(document).ready(function(){
			  initCCA();
		});		
		fetch("CardAuthEnrollService.php", {
			method: "POST", // *GET, POST, PUT, DELETE, etc.

			body: JSON.stringify(purchase), // body data type must match "Content-Type" header
		})
		.then(r =>  r.json())
		.then(data => 	bin_process(data))
		.catch(error => console.log(error));

		Cardinal.on('payments.setupComplete', function(setupCompleteData){
			console.log(JSON.stringify(setupCompleteData));

		});	
		Cardinal.on("payments.validated", function (vcard, jwt) {
			
		//Listen for Events
	    switch(vcard.ActionCode){

	      case "SUCCESS":
	      		console.log ("dataxxxxxxxxxxxxxx :"+JSON.stringify(vcard));
				var result = {...purchase,
                              ...vcard
                             };
				console.log(result);
			fetch("CardValidateService.php", {
				method: "POST", // *GET, POST, PUT, DELETE, etc.

				body: JSON.stringify(result), // body data type must match "Content-Type" header
			})
			.then(r =>  r.json())
			.then(data => validComplete(data));


		  break;

		  case "NOACTION":
			alert('NOACTION');

		  // Handle no actionable outcome
		  break;

		  case "FAILURE":
			 alert('FAILURE');

		  // Handle failed transaction attempt
		  break;

		  case "ERROR":
			 alert('ERROR:' +data.ErrorDescription);

		  // Handle service level error
		  break;
	  }
		});

				
		function bin_process(data){
			transactionId = data.payerAuthEnrollReply_authenticationTransactionID;
			Cardinal.trigger("bin.process", purchase.Account.AccountNumber)
				.then(function(results){

				if(results.Status) {
					// Bin profiling was successful. Some merchants may want to only move forward with CCA if profiling was successful

				} else {
					// Bin profiling failed
				}
				console.log(results.Status);
				card_continue(data);
			// Bin profiling, if this is the card the end user is paying with you may start the CCA flow at this point
				//Cardinal.start('cca', myOrderObject);
			  })
			  .catch(function(error){
			  	console.log(error);
				// An error occurred during profiling
			  })			

		}
		function valid_complete(validobj){

		}
		function card_continue(enrollobj){
			Cardinal.continue("cca", { 
				"AcsUrl":enrollobj.payerAuthEnrollReply_acsURL,
				"Payload":enrollobj.payerAuthEnrollReply_paReq,

			},
			{
			 "OrderDetails":{
				"TransactionId":enrollobj.payerAuthEnrollReply_authenticationTransactionID
			}
			});
		}


		function initCCA(){
			// get jwt container
			console.log(JSON.stringify(document.getElementById("JWTContainer").value));
			Cardinal.setup("init", {
			    jwt: document.getElementById("JWTContainer").value,
				order: orderObject
			});

		}
	</script>
<input type="hidden" id="JWTContainer" value= "<?php echo $jwt;?>" />
<div id="accno"></div>
 
</body>

</html>