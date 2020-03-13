<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\softDeletes;

class Employee extends Model
{
    use SoftDeletes;

    protected $table = "employees";
    public $timestamps = false;
    protected $fillable = ["first_name","last_name","email","password","salary","address_id","job_id"];
    protected $dates = ["deleted_at"];
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