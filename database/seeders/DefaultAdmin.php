<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DefaultAdmin extends Seeder {

	/**
	 * Inserts the default admin username and password (should be changed
	 * immediately upon first login!)
	 *
	 * @return void
	 */
	public function run() {

		DB::table('admins')->insert([
			'username' => 'admin',
			'password' => bcrypt('password'),
		]);
	}
}
