<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;






class Supplier extends Model
{
    protected $fillable = ['companyname', 'vatnumber', 'email','addressId','phonenumber'];
    public $timestamps = false;
}
