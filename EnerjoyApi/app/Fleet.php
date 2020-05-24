<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Fleet extends Model
{
    protected $fillable = ['brand', 'model', 'licenseplate', 'owner_id'];
    protected $with = ['employee'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at', 'owner_id'];

    use SoftDeletes;

    public function employee()
    {
        return $this->hasOne('App\Employee', 'id', 'owner_id');
    }
}
