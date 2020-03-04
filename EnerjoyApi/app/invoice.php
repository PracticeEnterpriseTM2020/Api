<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class invoice extends Model
{
    protected $fillable = ['id', 'customerId', 'price','date'];
    public function customer()
    {
        return $this->belongsTo('App\customer','customerId');
    }      
}