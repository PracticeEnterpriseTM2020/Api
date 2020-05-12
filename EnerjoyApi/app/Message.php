<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
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
