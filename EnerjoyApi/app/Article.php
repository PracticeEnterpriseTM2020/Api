<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use SoftDeletes;

    protected $fillable = ["title", "description", "creator_id"];
    protected $hidden = ["updated_at", "deleted_at", "creator_id"];
    protected $with = ["creator"];

    public function creator()
    {
        return $this->hasOne("App\Employee", "id", "creator_id");
    }
}
