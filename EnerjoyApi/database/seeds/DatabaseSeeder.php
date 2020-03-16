<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(CustomerTableSeeder::class);
        $this->call(AddressesTableSeeder::class);
        $this->call(CitiesTableSeeder::class);
        $this->call(CountriesTableSeeder::class);
        $this->call(EmployeesTableSeeder::class);
        $this->call(JobTableSeeder::class);
        $this->call(JobOfferTableSeeder::class);
        $this->call(FleetTableSeeder::class);
    }
}
