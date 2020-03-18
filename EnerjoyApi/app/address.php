<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class address extends Model
{
    protected $table = 'addresses';
    protected $fillable = ['street', 'number', 'cityId'];
    public $timestamps = false;
    public function customer()
    {
        return $this->hasOne('App\customer');
    }

    public function city()
    {
        return $this->belongsTo('App\city','cityId');
    }
}