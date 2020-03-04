<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Meters extends Model
{
    public $timestamps = false;
    protected $fillable = ['meter_id', 'creation_timestamp', 'isUsed'];
}
