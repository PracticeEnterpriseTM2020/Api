<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Conversation extends Model
{
    use SoftDeletes;

    protected $fillable = ["employee_one_id", "employee_two_id"];
    protected $hidden = ["created_at", "deleted_at"];
    protected $with = ["last_message", "employee_one", "employee_two"];

    function last_message()
    {
        return $this->hasOne("App\Message")->latest();
    }

    function employee_one()
    {
        return $this->belongsTo("App\Employee", "employee_one_id", "id");
    }

    function employee_two()
    {
        return $this->belongsTo("App\Employee", "employee_two_id", "id");
    }
}
