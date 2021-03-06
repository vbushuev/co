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
class StatusRequest extends Request{
    public function __construct($d){
        $d["operation"] = "status";
        $d["fields"] = ["client_orderid","login","orderid","by-request-sn","control"];
        $d["control"] = ["login","client_orderid","orderid","merchant_control"];
        parent::__construct($d);
    }
}
?>
