<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class meter_data extends Model
{
    public $timestamps = false;
    protected $fillable = ['Connection_ID', 'meterReading', 'totalMeterReading', 'startReadDate', 'readDate'];
}
