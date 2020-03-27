<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\softDeletes;

class Employee extends Model
{
    use SoftDeletes;

    protected $table = "employees";
    protected $fillable = ["first_name","last_name","email","password","salary","address_id","job_id"];
    protected $hidden = ['created_at','updated_at','password','deleted_at'];
    protected $dates = ["deleted_at"];
    protected $with = ["address","job"];

    function address()
    {
        return $this->BelongsTo("App\address");
    }

    function job()
    {
        return $this->belongsTo("App\Job");
    }

}