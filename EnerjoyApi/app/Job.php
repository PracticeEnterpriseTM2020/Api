<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    protected $table = "jobs";
    protected $fillable = ["job_title"];
    protected $width = ["Employee"];

    function employee()
    {
        return $this->hasMany("App/Employee");
    }
}
