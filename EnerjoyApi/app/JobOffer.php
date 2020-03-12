<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JobOffer extends Model
{
    public $timestamps = false;
    protected $fillable = ['job_offer_title', 'job_offer_description', 'job_id', 'creator_id'];
    protected $with = ['Employee', 'Job'];

    public function employee()
    {
        return $this->hasOne('App\Employee','creator_id');
    }

    public function job()
    {
        return $this->hasOne('App\Job');
    }
}
