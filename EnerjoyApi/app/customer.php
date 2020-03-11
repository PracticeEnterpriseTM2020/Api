<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class customer extends Model
{
    public $timestamps = false;
    protected $fillable = ['firstname', 'lastname', 'email','password','addressId'];
    public function address()
    {
        return $this->belongsTo('App\address','addressId');
    }
}