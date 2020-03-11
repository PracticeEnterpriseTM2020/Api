<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class customer extends Model
{
    public $timestamps = false;
    protected $fillable = ['firstname', 'lastname', 'email','password'];
    public function address()
    {
        return $this->belongsTo('App\address','addressId');
    }

    public function invoice()
    {
        return $this->hasOne('App\invoice');
    }
}