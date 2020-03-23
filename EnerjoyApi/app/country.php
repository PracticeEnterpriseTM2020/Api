<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class country extends Model
{
    public $timestamps = false;

    protected $table = 'countries';
    public function city()
    {
        return $this->hasMany('App\city');
    }
}
