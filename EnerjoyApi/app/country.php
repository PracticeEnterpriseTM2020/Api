<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class country extends Model
{
    public function address()
    {
        return $this->hasOne('App\address');
    }
}
