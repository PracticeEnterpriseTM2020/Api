<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class FleetTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        for ($i = 0; $i < 10; $i++) {
            DB::table('fleets')->insert([
                'brand' => $faker->word,
                'model' => $faker->word,
                'licenseplate' => $faker->bothify("1-???-###"),
                'owner_id' => $i
            ]);
        }
    }
}
