<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\softDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Employee extends User implements JWTSubject
{
    use SoftDeletes;

    protected $table = "employees";
    protected $fillable = ["first_name", "last_name", "email", "password", "salary", "phone", "ssn", "birthdate", "address_id", "job_id"];
    protected $hidden = ['created_at', 'updated_at', 'password', 'deleted_at', 'address_id', 'job_id'];
    protected $dates = ["deleted_at"];
    protected $with = ["address", "job"];

    function address()
    {
        return $this->belongsTo("App\address");
    }

    function job()
    {
        return $this->belongsTo("App\Job");
    }

    function fleet()
    {
        return $this->hasMany("App\Fleet");
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
