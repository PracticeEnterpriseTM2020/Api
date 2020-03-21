<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class JobsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('jobs')->insert([
            'job_title' => 'human resources'
        ]);
        $faker = Faker::create();
        foreach (range(1, 10) as $index) {
            DB::table('jobs')->insert([
                'job_title' => $faker->jobTitle
            ]);
        }
    }
}
