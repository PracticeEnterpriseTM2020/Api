<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobOffer extends Model
{
    protected $fillable = ['job_offer_title', 'job_offer_description', 'job_id', 'creator_id'];
    protected $with = ['employee', 'job'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    use SoftDeletes;

    public function employee()
    {
        return $this->hasOne('App\Employee','id', 'creator_id');
    }

    public function job()
    {
        return $this->hasOne('App\Job', 'id', 'job_id');
    }
}
