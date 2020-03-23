<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\softDeletes;

class Job extends Model
{
    use SoftDeletes;

    protected $table = "jobs";
    protected $fillable = ["job_title"];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    protected $dates = ["deleted_at"];

    function employee()
    {
        return $this->hasMany("App\Employee");
    }
}
