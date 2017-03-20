<?php
/*******************************************************************************
 ** GetCardRef Request Parameters
 * login	20/String	Merchant login name	Mandatory
 * cardrefid	20/String	Equals to card-ref-id obtained in Card Information Reference ID call during Card Registration stage	Mandatory
 * control	128/String	Checksum used to ensure that it is Merchant (and not a fraudster) that initiates the return request. This is SHA-1 checksum of the concatenation login + cardrefid + merchant_control.	Mandatory
 *******************************************************************************/
namespace Vsb\Gateway\Ariuspay;
use \Vsb\Gateway\Ariuspay\Exception as VsbGatewayAruispayException;
use \Vsb\Gateway\Ariuspay\Request as Request;
class RebillRequest extends Request{
    public function __construct($d){
        $d["operation"] = "make-rebill";
        $d["fields"] = ["client_orderid","login","order_desc","cardrefid","amount","currency","enumerate_amounts","cvv2","ipaddress","control","redirect_url","server_callback_url"];
        $d["control"] = ["login","client_orderid","cardrefid","amount","currency","merchant_control"];
        parent::__construct($d);
    }
}
?>
