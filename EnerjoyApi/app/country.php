<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class country extends Model
{
    protected $table = 'country';
    public function city()
    {
        return $this->hasMany('App\city');
    }
}
