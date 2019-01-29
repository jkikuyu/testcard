<?php

//set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__) . '/conf');
//$dotenv = new Dotenv\Dotenv(__DIR__ . '/../../../../');
//$dotenv->load();

/**
 * CybsClient
 *
 * An implementation of PHP's SOAPClient class for making either name-value pair
 * or XML CyberSource requests.
 */
class CybsClient extends SoapClient
{
    const CLIENT_LIBRARY_VERSION = "CyberSource PHP 1.0.0";

    private $merchantId;
    private $transactionKey;
    

    function __construct($options=array(), $nvp=false)
    {
        $required = array('MERCHANT_ID', 'TRANSACTION_KEY');
        /*
        if (!$properties) {
            throw new Exception('Unable to read cybs.ini.');
        }
        */
        $merchantId = getenv('MERCHANT_ID');
        $transactionKey = getenv('TRANSACTION_KEY');                                                                

        if ($nvp === true) {
            array_push($required, 'NVP_WSDL_URL');
            $wsdl = getenv('NVP_WSDL_URL');
        } else {
            array_push($required, 'WSDL_URL');
            $wsdl =getenv('WSDL_URL');
        }

        foreach ($required as $req) {
            $res = getenv($req);
            if (empty($res)) {
                throw new Exception($req . ' not found in environment variables.');
            }
        }
        $options=['trace'=>1];
        parent::__construct($wsdl, $options);
        $this->merchantId = $merchantId;
        $this->transactionKey = $transactionKey;

        $nameSpace = "http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd";

        $soapUsername = new SoapVar(
            $this->merchantId,
            XSD_STRING,
            NULL,
            $nameSpace,
            NULL,
            $nameSpace
        );

        $soapPassword = new SoapVar(
            $this->transactionKey,
            XSD_STRING,
            NULL,
            $nameSpace,
            NULL,
            $nameSpace
        );

        $auth = new stdClass();
        $auth->Username = $soapUsername;
        $auth->Password = $soapPassword; 

        $soapAuth = new SoapVar(
            $auth,
            SOAP_ENC_OBJECT,
            NULL, $nameSpace,
            'UsernameToken',
            $nameSpace
        ); 

        $token = new stdClass();
        $token->UsernameToken = $soapAuth; 

        $soapToken = new SoapVar(
            $token,
            SOAP_ENC_OBJECT,
            NULL,
            $nameSpace,
            'UsernameToken',
            $nameSpace
        );

        $security =new SoapVar(
            $soapToken,
            SOAP_ENC_OBJECT,
            NULL,
            $nameSpace,
            'Security',
            $nameSpace
        );

        $header = new SoapHeader($nameSpace, 'Security', $security, true); 
        $this->__setSoapHeaders(array($header)); 
    }

    /**
     * @return string The client's merchant ID.
     */
    public function getMerchantId()
    {
        return $this->merchantId;
    }

    /**
     * @return string The client's transaction key.
     */
    public function getTransactionKey()
    {
        return $this->transactionKey;
    }
}