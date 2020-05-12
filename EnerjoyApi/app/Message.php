<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Message extends Model
{
    use SoftDeletes;

    protected $fillable = ["text", "sender_id", "conversation_id"];
    protected $hidden = ["updated_at", "deleted_at"];
    protected $with = ["sender"];

    function sender()
    {
        return $this->belongsTo("App\Employee", "sender_id", "id");
    }

    function conversation()
    {
        return $this->belongsTo("App\Conversation");
    }
}
