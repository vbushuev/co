<?php
namespace Vsb\Gateway\Finkarta;
use \Vsb\Vsb as Vsb;
use \Vsb\Gateway\Finkarta\Exception as VsbGatewayException;
use \Vsb\Gateway\Finkarta\Request as FinkartaRequest;
use \Vsb\Gateway\Finkarta\Response as FinkartaResponse;
class Connector implements \Vsb\Interfaces\IConnector{
    public function call($request){

        $response = $this->query($request->getUrl(),["file"=>'@/'.$request->getRequestFile()]);
        return $response;
    }
    public function query($url,$data = null){
        $fp=fopen('../curl-'.date("Y-m-d").'.log', 'a+');
        $curlOptions = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_VERBOSE => 1,
            CURLOPT_STDERR => $fp,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_FOLLOWLOCATION => true
        ];
        $curl = curl_init($url);
        curl_setopt_array($curl, $curlOptions);
        $response = curl_exec($curl);
        Vsb::debug("RAW RESPONSE:[{$response}]");
        return $response;
    }
};
?>
