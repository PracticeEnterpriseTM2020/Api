<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class meter_customer extends Model
{
    public $timestamps = false;
    protected $fillable = ['customer_email', 'meter_id', 'installedOn'];
}
