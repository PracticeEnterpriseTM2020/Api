<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class country extends Model
{
    use SoftDeletes;
    
    public $timestamps = false;

    protected $table = 'countries';
    protected $fillable = ["iso", "name", "nicename", "iso3", "numcode", "phonecode"];
    protected $hidden = ["created_at", "updated_at"];

    public static function boot()
    {
        parent::boot();
        
        self::deleting(function (country $country) {
            $country->load("city");

            foreach ($country->city as $city) {
                $city->country_id = Country::all()->get(1)->id;
                $city->save();
            }
        });
    }

    public function city(){
        return $this->hasMany("App\city");
    }

}
