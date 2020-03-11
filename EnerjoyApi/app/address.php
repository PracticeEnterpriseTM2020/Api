<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class address extends Model
{
    protected $table = 'addresses';
    protected $fillable = ['street', 'number', 'city','postalcode'];
    public function customer()
    {
        return $this->hasOne('App\customer');
    }

    public function city()
    {
        return $this->belongsTo('App\city','cityId');
    }
}