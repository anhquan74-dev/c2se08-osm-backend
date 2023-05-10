<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserSyncRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = User::find(1);
	    $admin->assignRole( 'admin' );
	    $provider = User::find(2);
	    $provider->assignRole( 'provider' );
		$customer = User::find(3);
	    $customer->assignRole( 'customer' );
        $customer = User::find(4);
        $customer->assignRole( 'customer' );
    }
}
