<?php

namespace App\Http\Controllers;

use Log;
use App\Order;
use App\Addresses;
use App\Cards;
use App\User;
use Vsb\Gateway\Ariuspay\Connector;
use Vsb\Gateway\Ariuspay\Ariuspay;
use Vsb\Gateway\Ariuspay\PreauthRequest;
use Vsb\Gateway\Ariuspay\CaptureRequest;
use Vsb\Gateway\Ariuspay\SaleRequest;
use Vsb\Gateway\Ariuspay\CallbackResponse;
use Vsb\Gateway\Ariuspay\CreateCardRefRequest;
use Illuminate\Http\Request;

class CoController extends Controller
{
    public function __construct(){
        $this->middleware('cors');
    }
    public function index($id){
        $o = Order::find($id);
        return response()->json($o,200,['Content-Type' => 'application/json; charset=utf-8'],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }
    public function create(Request $rq){
        $res = Order::create($this->formatInput($rq));
        return response()->json($res,200,['Content-Type' => 'application/json; charset=utf-8'],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }
    public function update(Request $rq){
        $data = $this->formatInput($rq);
        $res = Order::find($data["id"]);
        $res->fill($data);
        $res->save();
        return response()->json($res,200,['Content-Type' => 'application/json; charset=utf-8'],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }
    public function pay(Request $rq){
        $data = $this->formatInput($rq);
        $res = [];
        $order = Order::find($data["id"]);
        if($data["status_id"]=="4.1"){
            $res = $data;
        }
        else if($data["status_id"]=="4.3" && $data["payment_id"]=="1"){
            $res = $data;
            $order->fill([
                "status_id"=>"4",
                "payment_id"=>$data["payment_id"],
                "card_id"=>$data["card_id"]
            ]);
            $res["status_id"] = "4";
        }
        elseif($data["status_id"]=="4.2" ){
            $order->fill(["payment_id"=>$data["payment_id"],"status_id"=>"3"]);
            $user = User::find($order->user_id);
            $address = Addresses::find($order->address_id);
            $response = $this->payout($order,$user,$address,$rq);
            $res = array_merge($order->toArray(),$response->get());
            $res["status_id"] = "4.2";
        }
        $order->save();
        return response()->json($res,200,['Content-Type' => 'application/json; charset=utf-8'],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }
    public function payoutresponse(Request $rq){
        $data = $rq->getContent();
        $res = [];
        parse_str($data,$res);
        $urldata = $res;
        $order = Order::find($res["client_orderid"]);
        $urldata["status_id"]=$order->status_id;
        $urldata["_com_response"]=$order->id;
        $urldata=array_merge($urldata,Ariuspay::$gates[config("app.aquire")][ucfirst($res["type"])."Request"]);
        $urldata["amount"]=($order->payment_id == "1")?1:($order->amount+$order->shipping_fee+$order->service_fee);
        $urldata["tx_date"]=date("Y-m-d H:i:s");
        $r = ["url" => isset($_SERVER["HTTP_ORIGIN"])?$_SERVER["HTTP_ORIGIN"]:$_SERVER["HTTP_HOST"],"data" => $res];
        try{
            $obj = new CallbackResponse($r,function($d){});
            if($obj->accept()){
                $operation = ($order->payment_id == "1")?"CreateCardRef_RIB":"CreateCardRef";
                $crdData = array_merge(Ariuspay::$gates[config("app.aquire")][$operation],["data"=>[
                    'client_orderid' => $obj->client_orderid,
                    'orderid' => $obj->orderid
                ]]);
                $request = new CreateCardRefRequest($crdData);
                $connector = new Connector();
                $connector->setRequest($request);
                $connector->call();
                $key = "card-ref-id";
                $dta = getdate(time());
                $card = Cards::where("cardref",$connector->getResponse()->$key)->first();
                if(!isset($card->id))$card = Cards::create([
                    "name"=>$res["card-type"]." ".$res["bin"]."xxxxxx".$res["last-four-digits"],
                    "cardref"=>$connector->getResponse()->$key,
                    "user_id"=>$order->user_id,
                    "expire"=>strftime('%Y-%m-%d 23:59:59',mktime(23,59,59,$dta['mon']+1,$dta['mday'],$dta['year']))
                ]);
                $order->fill([
                    "status_id"=>"4",
                    "card_id"=>$card->id
                ]);
                $order->save();
                $urldata["status_id"]=$order->status_id;
            }
        }
        catch(\Exception $e){
            Log::error($e);
        }
        return redirect()->away($order->response_url."?".http_build_query($urldata));
    }
    protected function formatInput(Request $rq){
        $in = $rq->all();
        Log::debug($in);
        $data = [
            'amount' => isset($in["amount"])?$in["amount"]:"0",
            'currency' => isset($in["currency"])?$in["currency"]:"RUB",
            'status_id' => isset($in["status_id"])?$in["status_id"]:"1",
            'raw_request' => json_encode($in,JSON_UNESCAPED_UNICODE)
        ];
        $user = null;
        if(isset($in["id"]))$data['id']=$in["id"];
        if(isset($in["shop_id"]))$data['shop_id']=$in["shop_id"];
        //if(isset($in["internal_order_id"]))$data['internal_order_id']=$in["internal_order_id"];
        //if(isset($in["external_order_id"]))$data['external_order_id']=$in["external_order_id"];
        //if(isset($in["external_order_url"]))$data['external_order_url']=$in["external_order_url"];
        if(isset($in["response_url"]))$data['response_url']=$in["response_url"];
        if(isset($in["user_id"])){
            $data['user_id']=$in["user_id"];
            $user = User::find($data['user_id']);
        }
        if(isset($in["payment_id"]))$data['payment_id']=$in["payment_id"];
        if(isset($in["delivery_id"]))$data['delivery_id']=$in["delivery_id"];
        if(isset($in["service_fee"]))$data['service_fee']=$in["service_fee"];
        if(isset($in["card_ref"]))$data['card_ref']=$in["card_ref"];
        if(isset($in["shipping_fee"]))$data['shipping_fee']=$in["shipping_fee"];
        if(isset($in["shipping_tracker"]))$data['shipping_tracker']=$in["shipping_tracker"];
        if(isset($in["user_email"]) && isset($in["user_phone"])){
            $user = User::where("phone",$in["user_phone"])->first();
            if(!isset($user->id))$user = User::where("email",$in["user_email"])->first();
            if(!isset($user->id)){
                $user = User::create([
                    'name' => $in['user_email'],
                    'email' => $in['user_email'],
                    'phone' => $in['user_phone'],
                    'password' => bcrypt($in['user_phone']),
                ]);
            }
            $data["user_id"] = $user->id;
        }
        elseif ( !is_null($user) &&(
            isset($in["user_passport_series"]) ||
            isset($in["user_passport_number"]) ||
            isset($in["user_passport_date"]) ||
            isset($in["user_passport_issue"]) ||
            isset($in["user_birthdate"]) ||
            isset($in["user_name"]) ||
            isset($in["user_middlename"]) ||
            isset($in["user_lastname"])
        )) {
            $user->fill([
                "passport_series" => isset($in["user_passport_series"])?$in["user_passport_series"]:"",
                "passport_number" => isset($in["user_passport_number"])?$in["user_passport_number"]:"",
                "passport_date" => isset($in["user_passport_date"])?$in["user_passport_date"]:"",
                "passport_issue" => isset($in["user_passport_issue"])?$in["user_passport_issue"]:"",
                "birthdate" => isset($in["user_birthdate"])?$in["user_birthdate"]:"",
                "name" => isset($in["user_name"])?$in["user_name"]:"",
                "middlename" => isset($in["user_middlename"])?$in["user_middlename"]:"",
                "lastname" => isset($in["user_lastname"])?$in["user_lastname"]:""
            ]);
            $user->save();
            $data["status_id"]="3";
        }
        if( !is_null($user) && (isset($in["address_postcode"])&&isset($in["address_country"])&&isset($in["address_city"])&&isset($in["address_address"]))){
            $addrs = Addresses::where("user_id",$user->id)->get();$needadd = true;$address_id=null;
            foreach ($addrs as $addr) {
                $needadd = !($addr->postcode == $in["address_postcode"] & $addr->country = $in["address_country"] & $addr->city = $in["address_city"] & $addr->address = $in["address_address"]);
                if(!$needadd){$address_id=$addr->id;break;}
            }
            if($needadd){
                $addr = Addresses::create([
                    "user_id"=>$user->id,
                    "postcode" => $in["address_postcode"],
                    "country" => $in["address_country"],
                    "city" => $in["address_city"],
                    "address" => $in["address_address"]
                ]);
                $address_id = $addr->id;
            }
            $data["address_id"] = $address_id;
        }
        if(isset($data["payment_id"])){
            // to do
            /*
             * if isset cards let choose cards
             * or let add go to card add
             */
            $data["status_id"]="4";
            if(isset($in["card_id"])){//user already choosen card
                //$card = Cards::find($in["card_id"]);
                $data["card_id"] = $in["card_id"];
                $data["status_id"]="4.3";
            }else{
                $cards = Cards::where("user_id",$user->id)->get();
                if(count($cards)){ //let use choose card
                    $data["status_id"]="4.1";
                    $data["cards"] = $cards;
                }else{ // let user add card
                    $data["status_id"]="4.2";
                }
            }

        }
        else if(isset($data["status_id"]) && $data["status_id"]=="3")$data["status_id"]="3";
        else if(isset($data["delivery_id"]))$data["status_id"]="2";
        else if(isset($data["user_id"]))$data["status_id"]="1";
        Log::debug($data);
        return $data;
    }
    protected function payout(Order $order,User $user,Addresses $address,Request $rq){
        $operation = "PreauthRequest";
        $aquire = config("app.aquire");
        if($order->payment_id == "1") {
            $amount = 1;
            $operation = "PreauthRequest";
        }
        else {
            $amount = ($order->amount+$order->shipping_fee+$order->service_fee);
            $operation = "SaleRequest";
        }
        $saleData = [
            "data"=>[
                "client_orderid" => $order->id,
                "order_desc" => 'checkout request',
                "first_name" => $user->name,
                "last_name" => $user->lastname,
                "ssn" => "",
                "birthday" => $user->birthdate,
                "address1" => $address->address,
                "address2" => "",
                "city" => $address->city,
                "state" => "",//isset($data["state"])?$data["state"]:"",
                "zip_code" => $address->postcode,
                "country" => $address->country,
                "phone" => $user->phone,
                "cell_phone" => $user->phone,
                "amount" => $amount,
                "currency" => "RUB",
                "email" => $user->email,
                "ipaddress" => 	$rq->ip(),
                "site_url" => config("app.url"),
                /*"credit_card_number" => "4444555566661111",
                "card_printed_name" => "CARD HOLDER",
                "expire_month" => "12",
                "expire_year" => "2099",
                "cvv2" => "123",*/
                "purpose" => "www.garan24.eu",
                "redirect_url" => config("app.url")."/payoutresponse",
                "server_callback_url" =>  config("app.url")."/payoutcallback",
                //"merchant_data" => "VIP customer"
            ]
        ];

        $saleData = array_merge(Ariuspay::$gates[$aquire][$operation],$saleData);
        $request = new PreauthRequest($saleData);
        switch($operation){
            case "CaptureRequest":$request = new CaptureRequest($saleData);break;
            case "SaleRequest":$request = new SaleRequest($saleData);break;
        }
        $connector = new Connector();
        $connector->setRequest($request);
        $connector->call();
        return $connector->getResponse();
    }
    protected function getCard(){

    }
}
