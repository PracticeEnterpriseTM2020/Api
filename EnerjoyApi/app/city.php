<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class city extends Model
{
    public function address()
    {
        return $this->hasOne('App\address');
    }
}
