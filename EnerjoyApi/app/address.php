<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class address extends Model
{
    public $timestamps = false;
    protected $table = 'addresses';
    protected $fillable = ['street', 'number', 'city_id', 'postalcode'];
    protected $hidden = ["created_at", "updated_at"];
    protected $with = ["city"];

    public function city()
    {
        return $this->belongsTo('App\city');
    }
}
