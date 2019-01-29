<?php
    /**
    ** @author :jude
    **/
    namespace IpaySecure;

    use Lcobucci\JWT\Builder;
    use Lcobucci\JWT\Signer\Hmac\Sha256;
    use Firebase\JWT\JWT;
    use Dotenv\Dotenv;


        $dotenv = new Dotenv(__DIR__.'/secure');
        $dotenv->load();


    class JWTUtil {

        private $api_Key ;
        private $api_Id ;
        private $orgUnit_Id;
        function __construct() {
            /* test credentials*/

            $this->api_Key = getenv('API_KEY');
            $this->api_Id = getenv('API_ID');
            $this->orgUnit_Id = getenv('ORGUNIT_ID');

        }

        /*  $GLOBALS['ApiKey'] = '[INSERT_API_KEY_HERE]';
            $GLOBALS['ApiId'] = '[INSERT_API_KEY_ID_HERE]';
            $GLOBALS['OrgUnitId'] = '[INSERT_ORG_UNIT_ID_HERE]';
        */     

        /**
         * JWT Creation
         * https://cardinaldocs.atlassian.net/wiki/spaces/CC/pages/196850/JWT+Creation
         *
        **/
        function generateJwt($orderTransactionId, $orderObject, $referenceId){
            $currentTime = time();
            $expireTime = 3600; // expiration in seconds - this equals 1hr
            $token = (new Builder())->setId($orderTransactionId, true) // The Transaction Id (jti claim)
                        ->setIssuedAt($currentTime) // Configures the time that the token was issued (iat claim)
                        ->setExpiration($currentTime + $expireTime) // Configures the expiration time of the token (exp claim)
                        ->setIssuer($this->api_Id) // API Key Identifier (iss claim)
                        //->set('Payload', $orderObj) // Configures a new claim, called "Payload", containing the OrderDetails

                        ->set('OrgUnitId',   $this->orgUnit_Id) // Configures a new claim, called "OrgUnitId"
                       // ->set('ObjectifyPayload', true)
				   		->set('ReferenceId', $referenceId) // Configures a new claim, called "referenceId"
                        
						->sign(new Sha256(),  $this->api_Key) // Sign with API Key
                        ->getToken(); // Retrieves the generated token
         
            return $token; // The JWT String
        }

        function validateJwt($jwt) {
            // This will validate JWT Requests or Responses from Cardinal.
            $retval =false;
            try{
                // Validate the JWT by virtue of successful decoding
                $decoded = JWT::decode($jwt,$this->api_Key, array('HS256'));
                print_r($decoded);
                //echo json_encode($decoded);
                $retval = true;
            } catch (Exception $e) {
                echo "Exception in validateJwt: ", $e->getMessage(), "\n";

            }
            return $retval;    
        }
	}
?>

