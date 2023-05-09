<?php

namespace Database\Seeders;

use App\Models\User;
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
        $userArray = [1,2,3,4];
        foreach ($userArray as $user_id) {
            $user = User::find($user_id);
            if(isset($user)){
                $user->assignRole('customer');
            }
        }
    }
}
