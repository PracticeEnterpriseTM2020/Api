<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\softDeletes;

class Job extends Model
{
    use SoftDeletes;
    
    protected $table = "jobs";
    public $timestamps = false;
    protected $fillable = ["job_title"];
    protected $dates = ["deleted_at"];
    protected $width = ["Employee"];

    function employee()
    {
        return $this->hasMany("App/Employee");
    }
}
