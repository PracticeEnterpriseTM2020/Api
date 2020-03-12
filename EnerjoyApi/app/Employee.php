<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Employee extends Model
{
    protected $table = "employees";
    protected $fillable = ["first_name","last_name","email","password","salary","address_id","job_id"];
    protected $hidden = ["password"];
    protected $width = ["address","Job"];

    function address()
    {
        return $this->BelongsTo("App\address");
    }

    function job()
    {
        return $this->belongsTo("App\Job");
    }

}