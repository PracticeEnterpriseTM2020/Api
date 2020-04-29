<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class meter_data extends Model
{
    public $timestamps = false;
    protected $fillable = ['meter_id', 'meterReading', 'readDate'];
}
