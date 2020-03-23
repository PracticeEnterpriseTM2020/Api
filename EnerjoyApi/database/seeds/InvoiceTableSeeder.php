<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class InvoiceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        foreach (range(1, 10) as $index) {
            DB::table('invoices')->insert([
                'customerId' => $faker->numberBetween(1,10),
                'price' => $faker->numberBetween(1000,10000),
                'date' => $faker->unixTime
            ]);
        } 
    }
}
