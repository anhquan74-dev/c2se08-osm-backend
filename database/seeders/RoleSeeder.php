<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		$admin              = Role::create( [ 'name' => 'admin' ] );
		$serviceProvider    = Role::create( [ 'name' => 'provider' ] );
		$customer           = Role::create( [ 'name' => 'customer' ] );
		$adminPermission    = Permission::create( [ 'name' => 'admin' ] );
		$providerPermission = Permission::create( [ 'name' => 'provider' ] );
		$customerPermission = Permission::create( [ 'name' => 'customer' ] );
		$admin->givePermissionTo( $adminPermission );
		$serviceProvider->givePermissionTo( $providerPermission );
		$customer->givePermissionTo( $customerPermission );
	}
}