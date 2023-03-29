<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run()
    {
        $locations = [
            [
                'id' => 1,
                'user_id' => 1,
                'address' => 'Da Nang City',
                'province_id' => 1,
                'province_name' => 'Da Nang',
                'district_id' => 1,
                'district_name' => 'Hai Chau',
                'ward_id' => 1,
                'ward_name' => 'Thuan Phuoc',
                'coords_latitude' => 40.71727401,
                'coords_longitude' => -74.00898606,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 2,
                'user_id' => 1,
                'address' => 'HCM City',
                'province_id' => 2,
                'province_name' => 'Ho Chi Minh City',
                'district_id' => 2,
                'district_name' => 'Binh Tan',
                'ward_id' => 2,
                'ward_name' => 'Ward 2',
                'coords_latitude' => 40.71727401,
                'coords_longitude' => -74.00898606,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];
        foreach ($locations as $location) {
            Location::create($location);
        }
    }
}