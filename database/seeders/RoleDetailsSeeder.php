<?php

namespace Database\Seeders;

use App\Models\RoleDetails;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleDetailsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roleDetails = [
            [
                'id' => 1,
                'role_name' => 'admin',
            ],
            [
                'id' => 2,
                'role_name' => 'provider',
            ],
            [
                'id' => 3,
                'role_name' => 'customer',
            ],
        ];
        foreach ($roleDetails as $role) {
            RoleDetails::create($role);
        }
    }
}