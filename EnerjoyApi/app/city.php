<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class city extends Model
{
    protected $table = 'city';
    protected $fillable = ['countryId', 'name','postalcode'];
    public $timestamps = false;
    public function address()
    {
        return $this->hasOne('App\address');
    }
    public function country()
    {
        return $this->belongsTo('App\country','countryId');
    }
}
