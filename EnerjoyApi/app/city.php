<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class city extends Model
{
    public $timestamps = false;
    protected $table = 'city';
    protected $fillable = ['name', 'postalcode', 'countryId'];
    public function address()
    {
        return $this->hasOne('App\address');
    }
    public function country()
    {
        return $this->belongsTo('App\country','countryId');
    }
}
