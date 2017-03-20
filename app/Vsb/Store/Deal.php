<?php
namespace \Vsb\Store;
use \Vsb\Vsb as GARAN24;
use \Vsb\Store\DBObject as VsbdbObject;
use \Vsb\Store\Exception as StoreException;
class Deal extends VsbdbObject{
    public function __construct($id){
        parent::__construct("{id:\"{$id}\"}");
        $this->sync();
    }
    protected function sync(){
        if(!isset($this->id))return;
        $this->execute("select * from ".$this->_dbdata["prefix"]."deals where id = ".$this->id);
    }
    public function __set($nc,$v){
        $n=strtolower($nc);
        parent::__set($nc,$v);
        if($n=="status"){
            $this->prepare("update ".$this->_dbdata["prefix"]."deals set status='".$v."' where id = ".$this->id);
        }
    }
};
?>
