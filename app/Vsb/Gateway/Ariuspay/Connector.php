<?php
namespace Vsb\Gateway\Ariuspay;
use \Vsb\Gateway\BaseConnector as BaseConnector;
use \Vsb\Gateway\Ariuspay\Request as AriuspayRequest;
use \Vsb\Gateway\Aruispay\Exception  as VsbGatewayAruispayException;
class Connector extends BaseConnector{
    public function __construct($req=false){
        if($req!==false) $this->setRequest($req);
    }
    public function setRequest($req){
        if($req instanceof AriuspayRequest) $this->_request = $req;
        else throw new VsbGatewayAruispayException("Object ".preg_replace("/\\\/",".",get_class($req))." is not instance of Ariuspay Request object.",500);
    }
}
?>
