<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;






class Leverancier extends Model implements JWTSubject
{
    protected $fillable = ['companyname', 'vatnumber', 'email','addressId','phonenumber'];

    public function getJWTIdentifier()
    {
      return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
      return [];
    }
}
