<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\JobOffer;

class JobOfferController extends Controller
{

    public function show($email)
    {
        return JobOffer::get();
    }
}
