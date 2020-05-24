<?php

namespace App;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use UserInterface;

class customer extends Model{
    public $timestamps = false;
    protected $fillable = ['firstname', 'lastname', 'email','password','addressId'];
    protected $hidden = ['password'];

    public function address()
    {
        return $this->belongsTo('App\address','addressId');
    }

    public function invoice()
    {
        return $this->hasOne('App\invoice');
    }
}