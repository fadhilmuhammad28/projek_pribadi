<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rate;

class RateSeeder extends Seeder
{
    public function run(): void
    {
        $rates = [
            ['vehicle_type' => 'motor', 'first_hour' => 3000, 'additional_hour' => 2000],
            ['vehicle_type' => 'mobil', 'first_hour' => 5000, 'additional_hour' => 3000],
            ['vehicle_type' => 'truk', 'first_hour' => 8000, 'additional_hour' => 5000],
        ];

        foreach ($rates as $rate) {
            Rate::updateOrCreate(
                ['vehicle_type' => $rate['vehicle_type']],
                $rate
            );
        }
    }
}

