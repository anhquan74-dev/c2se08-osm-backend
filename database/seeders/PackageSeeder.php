<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    public function run()
    {
        $packages = [
            [
                'id' => 1,
                'service_id' => 1,
                'name' => 'package name 1',
                'description' => 'description 1',
                'price' => 0,
                'total_rate' => 0,
                'total_star' => 0,
                'avg_star' => 0,
                'is_negotiable' => 0,
                'view_priority' => 0,
                'is_valid' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 2,
                'service_id' => 1,
                'name' => 'package name 2',
                'description' => 'description 2',
                'price' => 0,
                'total_rate' => 0,
                'total_star' => 0,
                'avg_star' => 0,
                'is_negotiable' => 0,
                'view_priority' => 0,
                'is_valid' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];
        foreach ($packages as $package) {
            Package::create($package);
        }
    }
}
