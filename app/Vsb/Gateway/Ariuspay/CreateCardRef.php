<?php
namespace Vsb\Gateway\Ariuspay;
use \Vsb\Gateway\BaseConnector as BaseConnector;
use \Vsb\Gateway\Ariuspay\CreateCardRefRequest as CreateCardRefRequest;
//use \Vsb\Gateway\Ariuspay\GetCardRefResponse as Response;
use \Vsb\Gateway\Aruispay\Exception  as VsbGatewayAruispayException;
class CreateCardRef extends BaseConnector{
    public function __construct($data=[]){
        $this->_request = new CreateCardRefRequest($data);
        //$this->_operation = "get-card-info";
    }
}
?>
