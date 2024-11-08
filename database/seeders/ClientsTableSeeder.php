<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;

class ClientsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('clients')->insert([
            ['name' => 'Client1', 'hourly_rate' => 140],
            ['name' => 'Client2', 'hourly_rate' => 170],
            // Add more clients as needed
        ]);
    }
}