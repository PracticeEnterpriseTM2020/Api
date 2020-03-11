<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;






class Leverancier extends Model
{
    protected $fillable = ['companyname', 'vatnumber', 'email','addressId','phonenumber'];

}
