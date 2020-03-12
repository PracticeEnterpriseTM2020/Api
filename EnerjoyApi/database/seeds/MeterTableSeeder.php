<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class MeterTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        for ($i = 0; $i <= 1000; $i++) {
            DB::table('meters')->insert([
                'meter_id' => $faker->bothify($text = '????????-??????-####-##'),
                'creation_timestamp' => $faker->unixTime,
                'isUsed' => $faker->boolean,
                'deleted' => $faker->boolean
            ]);
        }
    }
}
