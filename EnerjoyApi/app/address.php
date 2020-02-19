<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class address extends Model
{
    protected $fillable = ['address', 'number', 'city','postalcode','custId'];
    public function customer()
    {
        return $this->belongsTo('App\customer');
    }
}
