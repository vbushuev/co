<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Addresses extends Model{
    protected $fillable = [
        'type',
        'country',
        'city',
        'address',
        'postcode',
        'user_id'
    ];
    protected $table = 'addresses';
    public function setUpdatedAtAttribute($value){}
}
