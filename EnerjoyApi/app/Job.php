<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\softDeletes;

class Job extends Model
{
    use SoftDeletes;

    protected $table = "jobs";
    protected $fillable = ["job_title"];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    protected $dates = ["deleted_at"];

    public static function boot()
    {
        parent::boot();

        self::deleting(function (Job $job) {
            $job->load("employees");
            foreach ($job->employees as $emp) {
                $emp->job_id = null;
                $emp->save();
            }
        });
    }

    public function employees()
    {
        return $this->hasMany("App\Employee");
    }
}
