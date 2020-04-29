<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class supplier extends Model
{
    protected $fillable = ['companyname', 'vatnumber', 'email','addressId','phonenumber'];
    public $timestamps = false;
}
