<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Fleet extends Model
{
    protected $fillable = ['merk', 'model', 'nummerplaat', 'eigenaar_id'];
    protected $with = ['employee'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    use SoftDeletes;

    public function employee()
    {
        return $this->hasOne('App\Employee','id','eigenaar_id');
    }
}
