<?php
namespace Vsb\Deal;
use \Vsb\Object as G24Object;
class Variations extends G24Object{
    public function __construct($a=[]){
        $ii = is_array($a)?json_encode($a):$a;
        parent::__construct($ii);
    }
};
?>
