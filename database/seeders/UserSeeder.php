<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'id' => 1,
                'email' => 'admin@gmail.com',
                'password' => Hash::make("123456"),
                'full_name' => 'Administrator',
                'birthday' => date('Y-m-d H:i:s'),
                'gender' => 'Male',
                'phone_number' => "0123456789",
                'avatar' => 'avatar_admin',
                'introduction' => '',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 2,
                'email' => 'customer@gmail.com',
                'password' => Hash::make("123456"),
                'full_name' => 'Customer',
                'birthday' => date('Y-m-d H:i:s'),
                'gender' => 'Female',
                'phone_number' => "0123456789",
                'avatar' => 'avatar_customer',
                'introduction' => '',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 3,
                'email' => 'provider@gmail.com',
                'password' => Hash::make("123456"),
                'full_name' => 'Provider',
                'birthday' => date('Y-m-d H:i:s'),
                'gender' => 'Male',
                'phone_number' => "0123456789",
                'avatar' => 'avatar_provider',
                'introduction' => '',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        ];
        foreach ($users as $user) {
            User::create($user);
        }
    }
}