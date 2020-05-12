<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Conversation extends Model
{
    use SoftDeletes;

    protected $fillable = ["employee_one_id", "employee_two_id"];
    protected $hidden = ["created_at", "updated_at", "deleted_at"];
    protected $with = ["messages", "employee_one", "employee_two"];

    function messages()
    {
        return $this->hasMany("App\Message");
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
