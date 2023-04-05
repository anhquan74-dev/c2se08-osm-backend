<?php

namespace Database\Seeders;

use App\Models\RoleDetailUser;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleDetailUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roleDetailUsers = [
            [
                'id' => 1,
                'user_id' => 1,
                'role_details_id' => 1,
            ],
            [
                'id' => 2,
                'user_id' => 2,
                'role_details_id' => 2,
            ],
            [
                'id' => 3,
                'user_id' => 3,
                'role_details_id' => 3,
            ],
            [
                'id' => 4,
                'user_id' => 4,
                'role_details_id' => 3,
            ],
        ];
        foreach ($roleDetailUsers as $userRole) {
            RoleDetailUser::create($userRole);
        }
    }
}
