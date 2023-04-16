<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{

    public function run()
    {
        $services = [
            [
                'id' => 1,
                'category_id' => 1,
                'provider_id' => 2,
                'avg_price' => 0,
                'max_price' => 0,
                'min_price' => 0,
                'is_negotiable' => 1,
                'total_rate' => 0,
                'total_star' => 0,
                'avg_star' => 0,
                'number_of_packages' => 0,
                'is_valid' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 2,
                'category_id' => 1,
                'provider_id' => 2,
                'avg_price' => 0,
                'max_price' => 0,
                'min_price' => 0,
                'is_negotiable' => 1,
                'total_rate' => 0,
                'total_star' => 0,
                'avg_star' => 0,
                'number_of_packages' => 0,
                'is_valid' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];
        foreach ($services as $service) {
            Service::create($service);
        }
    }
}