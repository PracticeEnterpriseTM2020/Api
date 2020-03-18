<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class city extends Model
{
    protected $table = "cities";
    protected $fillable = ["name", "postalcode", "country_id"];
    protected $with = ["country"];

    public function address()
    {
        return $this->hasOne("App\address");
    }
    public function country()
    {
        return $this->belongsTo("App\country");
    }
}
