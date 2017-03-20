<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model {
    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    /*protected function validator(array $data){
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);
    }*/
    protected $fillable = [
        'amount',
        'currency',
        'shop_id',
        'status_id',
        'internal_order_id',
        'external_order_id',
        'external_order_url',
        'response_url',
        'user_id',
        'payment_id',
        'delivery_id',
        'service_fee',
        'card_ref',
        'shipping_fee',
        'card_id',
        'shipping_tracker',
        'address_id',
        'raw_request'
    ];
    protected $table = 'orders';
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

}
