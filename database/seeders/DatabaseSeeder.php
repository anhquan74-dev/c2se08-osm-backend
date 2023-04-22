<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $this->call([
            UserSeeder::class,
            RoleDetailsSeeder::class,
            RoleDetailUserSeeder::class,
            LocationSeeder::class,
            CategorySeeder::class,
            ServiceSeeder::class,
            PackageSeeder::class,
            AppointmentSeeder::class,
            FeedbackSeeder::class,
            FavoriteSeeder::class,
            NotifySeeder::class,
            MessageSeeder::class,
	        RoleSeeder::class,
	        UserSyncRoleSeeder::class
        ]);
    }
}
