<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class customer extends Model
{
    protected $fillable = ['firstname', 'lastname', 'email','password'];
    public function address()
    {
        return $this->belongsTo('App\address','addrId');
    }
}