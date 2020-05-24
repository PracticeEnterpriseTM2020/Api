<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Employee extends User implements JWTSubject
{
    use SoftDeletes;

    protected $table = "employees";
    protected $fillable = ["first_name", "last_name", "email", "password", "salary", "phone", "ssn", "birthdate", "address_id", "job_id"];
    protected $hidden = ['updated_at', 'password', 'deleted_at', 'address_id', 'job_id'];
    protected $dates = ["deleted_at"];
    protected $with = ["address", "job"];

    public static function boot()
    {
        parent::boot();

        self::deleting(function (Employee $emp) {
            $emp->load("fleet", "joboffers");
            foreach ($emp->fleet as $fleet) {
                $fleet->owner_id = null;
                $fleet->save();
            }
        });
    }

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
        return $this->hasMany("App\Fleet", "owner_id");
    }

    function joboffers()
    {
        return $this->hasMany("App\JobOffer", "creator_id");
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
