<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class address extends Model
{
    protected $fillable = ['street', 'number', 'city','postalcode'];
    public function customer()
    {
        return $this->hasOne('App\customer');
    }
    public function country()
    {
        return $this->belongsTo('App\country','countryId');
    }
    public function city()
    {
        return $this->belongsTo('App\city','cityId');
    }
}
