<?php
namespace Vsb\Shipping;
use Vsb\Object as GObject;
use Vsb\Shipping\Address as Address;
class Shipping extends GObject{
    public function __construct($a=[]){
        parent::__construct($a);
    }
    public function CheckAddress(Address $a){
        return true;
    }
    public function CalculateCost(Address $a){
        return new GObject('{"amount":"500.00","currency":"rub"}');
    }
};
?>
