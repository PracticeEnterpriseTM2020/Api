<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class JobOfferTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        for ($i = 1; $i <= 10; $i++) {
            DB::table('job_offers')->insert([
                'job_offer_title' => $faker->jobTitle,
                'job_offer_description' => $faker->text($maxNbChars = 50),
                'job_id' => $i,
                'creator_id' => $i
            ]);
        }
    }
}
