<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cards extends Model{
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
        'cardref',
        'name',
        'user_id',
        'expire'
    ];
    protected $table = 'cards';
    public function setUpdatedAtAttribute($value){}
}
