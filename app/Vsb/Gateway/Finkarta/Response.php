<?php
namespace \Vsb\Gateway\Finkarta;
use \Vsb\Gateway\Finkarta\Exception as VsbGatewayException;
class Response{
    protected $_url = "https://testrequest.f-karta.ru/";
    public function __construct($d=[
            "url" => "https://testrequest.f-karta.ru/",
            "data" => []
        ]){
        $this->_url = $d["url"];
    }
    public function getUrl(){
        return $this->_url;
    }
    public function __toString(){
        return "URL:".$this->getUrl().Vsb::obj2str($this->_params);
    }
    public function buildResponse($res){
        $rs = new FinkartaResponse([
            "url"=>$this->_url,
            "request"=>$this->build(),
            "data" => $res
        ]);
        return $rs;
    }
};
?>
