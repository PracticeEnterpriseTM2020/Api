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
        for ($i = 1; $i <= 1000; $i++) {

            $meter_id           = $faker->bothify($text = '????????-??????-####-##');
            $creation_timestamp = $faker->unixTime;
            $isUsed             = $faker->boolean;
            $deleted            = $faker->boolean;

            DB::table('meters')->insert([
                'meter_id' => $meter_id,
                'creation_timestamp' => $creation_timestamp,
                'isUsed' => $isUsed,
                'deleted' => $deleted
            ]);

            //$this->command->line('Inserting row ' . $i . ' | meter_id: ' . $meter_id . ' | creation_timestamp: ' . $creation_timestamp . ' | isUsed: ' . $isUsed . ' | deleted: ' . $deleted);
        }
    }
}
