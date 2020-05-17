<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class kosten extends Model
{
    protected $fillable = ['type', 'supplierId', 'prijs_per_eenheid'];
    public $timestamps = false;
}
