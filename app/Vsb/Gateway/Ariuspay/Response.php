<?php
/*******************************************************************************
 ** Sale Request Parameters
 * @param string[128]	client_orderid	- 	Merchant order identifier.
 * @param string[64k]	order_desc	- 	Brief order description
 * @param string[128]	card_printed_name	- 	Card printed name
 * @param string[50]	first_name	- 	Customer’s first name
 * @param string[50]	last_name	- 	Customer’s last name
 * @param int[4]|null	ssn	- 	Last four digits of the customer’s social security number.
 * @param int[8]|null	birthday	- 	Customer’s date of birth, in the format YYYYMMDD.
 * @param string[50]	address1	- 	Customer’s address line 1.
 * @param string[50]	city	- 	Customer’s city.
 * @param string[2]|null	state	- 	Customer’s state . Please see Reference for a list of valid state codes.
 * @param string[10]	zip_code	- 	Customer’s ZIP code
 * @param string[2]	country	- 	Customer’s country(two-letter country code). Please see Reference for a list of valid country codes.
 * @param string[15]	phone	- 	Customer’s full international phone number, including country code.
 * @param string[15]|null	cell_phone	- 	Customer’s full international cell phone number, including country code.
 * @param string[50]	email	- 	Customer’s email address.
 * @param int[10]	amount	- 	Amount to be charged. The amount has to be specified in the highest units with . delimiter. For instance, 10.5 for USD means 10 US Dollars and 50 Cents
 * @param string[3]	currency	- 	Currency the transaction is charged in (three-letter currency code). Sample values are: USD for US Dollar EUR for European Euro
 * @param int[20]	credit_card_number	- 	Customer’s credit card number.
 * @param int[2]	expire_month	- 	Credit card expiration month
 * @param int[4]	expire_year	- 	Credit card expiration year
 * @param int[3..4]	cvv2	- 	Customer’s CVV2 code. CVV2 (Card Verification Value) is a three- or four-digit number AFTER the credit card number in the signature area of the card.
 * @param string[20]	ipaddress	- 	Customer’s IP address, included for fraud screening purposes.
 * @param string[128]|null	site_url	- 	URL the original Sale is made from.
 * @param string[16..19]|null	destination-card-no	- 	Card number of destination card. for Money Send transactions
 * @param string[128]|null	purpose	- 	Destination to where the payment goes. It is useful for the merchants who let their clients to transfer money from a credit card to some type of client’s account, e.g. game or mobile phone account. Sample values are: +7123456789; gamer0001@ereality.com etc. This value will be used by fraud monitoring system.
 * @param string[40]	control	- 	Checksum generated by SHA-1. See Request authorization through control parameter for more details.
 * @param string[128]	redirect_url	- 	URL the cardholder will be redirected to upon completion of the transaction. Please note that the cardholder will be redirected in any case, no matter whether the transaction is approved or declined. You should not use this parameter to retrieve results from PaynetEasy gateway, because all parameters go through client’s browser and can be lost during transmission. To deliver the correct payment result to your backend use server_callback_url instead. Pass http://google.com if you use non3D schema for transactions processing and you have no need to return customer anywhere.
 * @param string[128]|null	server_callback_url	- 	URL the transaction result will be sent to. Merchant may use this URL for custom processing of the transaction completion, e.g. to collect sales data in Merchant’s database. See more details at Merchant Callbacks
 *******************************************************************************/

namespace Vsb\Gateway\Ariuspay;
use \Vsb\Vsb as Vsb;
use \Vsb\Gateway\HTTPResponse as HTTPResponse;
use \Vsb\Gateway\Ariuspay\Exception as VsbGatewayAruispayException;
use \Vsb\Gateway\Ariuspay\Request as Request;
class Response extends HTTPResponse{
    protected $_endpoint;
    protected $_merchant_key;
    protected $_merchant_login;
    protected $_url;
    protected $_operation;
    protected $_control_fields;
    public function __construct($d=[
            "url" => "https://sandbox.ariuspay.ru/paynet/api/v2/",
            "request" => "",
            "endpoint" => "1144",
            "merchant_key" => "99347351-273F-4D88-84B4-89793AE62D94",
            "merchant_login" => "GARAN24",
            "operation" => "sale-form",
            "fields" => [
                "error-message","error-code","type","status","serial-number",
                "card-printed-name","bin","last-four-digits",
                "expire-year","expire-month"
            ],
            "control" => ["endpoint","client_orderid","amount","email","merchant_control"],
            "data" => []
        ]){
        
        $constructData = [
            "url"=>$d["url"],
            "request"=>$d["request"],
            "data"=>$d["data"]
        ];
        if(isset($d["fields"]))$constructData["fields"]=$d["fields"];
        parent::__construct($constructData);
        $this->_endpoint = isset($d["endpoint"])?$d["endpoint"]:"1144";
        $this->_operation = isset($d["operation"])?$d["operation"]:"";
        $this->_merchant_login = isset($d["merchant_login"])?$d["merchant_login"]:"GARAN24";
        $this->_merchant_key = isset($d["merchant_key"])?$d["merchant_key"]:"99347351-273F-4D88-84B4-89793AE62D94";
        $this->_control_fields = $d["control"];
    }
    public function check(){
        if(!isset($this->_params["type"])){
            throw new VsbGatewayAruispayException("Error in response. Wrong format",500);
        }
        if($this->buildChecksum()!==$this->_params["control"]) throw new VsbGatewayAruispayException("Checksum is invalid",500);
        if(in_array(trim($this->_params["type"]),["validation-error","error"])){
            throw new VsbGatewayAruispayException(
                isset($this->_params["error-message"])?$this->_params["error-message"]:"Unknown message",
                isset($this->_params["error-code"])?$this->_params["error-code"]:500
            );
        }
        return true;
    }
    protected  function buildChecksum(){
        $str = "";
        foreach($this->_control_fields as $k){
            switch($k){
                case "endpoint":
                    $str.=$this->_endpoint;
                    break;
                case "merchant_control":
                    $str.=$this->_merchant_key;
                    break;
                case "login":
                    $str.=$this->_merchant_login;
                    break;
                default:
                    if(!isset($this->_params["{$k}"])){
                        throw new VsbGatewayAruispayException("Control field {$k} is not set.",403);
                    }
                    $str.=$this->_params["{$k}"];
                    break;
            }
        }
        return $this->checksum($str);
    }
    protected function checksum($str){
        $str = preg_replace("/[\r\n]+/","",$str);
        $res = sha1($str);
        Vsb::debug("Make checksum SHA1:what:[{$str}]:get:{{$res}}");
        return $res;
    }
}
?>
