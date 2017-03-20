<?php

namespace App\Http\Controllers;

use Log;
use App\Order;
use App\User;
use App\Cards;
use App\Adresses;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(){
        $this->middleware('cors');
    }
    public function index($id){
        $res = User::find($id);
        return response()->json($res,200,['Content-Type' => 'application/json; charset=utf-8'],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }
    public function cards($id){
        $res = Cards::where("user_id",$id)->get();
        return response()->json($res,200,['Content-Type' => 'application/json; charset=utf-8'],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }
    public function adresses($id){
        $res = Adresses::where("user_id",$id)->get();
        return response()->json($res,200,['Content-Type' => 'application/json; charset=utf-8'],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }
}
