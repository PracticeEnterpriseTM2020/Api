<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class country extends Model
{
    protected $table = 'countries';
    protected $fillable = ["name"];

    public function city()
    {
        return $this->hasMany('App\city');
    }
}
