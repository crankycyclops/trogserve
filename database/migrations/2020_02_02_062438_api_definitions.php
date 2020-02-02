<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ApiDefinitions extends Migration {

	/**
	 * Run the migration.
	 *
	 * @return void
	 */
	public function up() {
	
		Schema::create('definitions', function (Blueprint $table) {

			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_unicode_ci';

			$table->bigIncrements('id');
			$table->string('title')->nullable();
			$table->string('author')->nullable();
			$table->string('path')->nullable(false);
			$table->timestamp('created_at')->default(\DB::raw('CURRENT_TIMESTAMP'));
			$table->timestamp('updated_at')->default(\DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
			$table->timestampTz('last_uploaded')->nullable();
		});
	}

	/**
	 * Reverse the migration.
	 *
	 * @return void
	 */
	public function down() {

		Schema::dropIfExists('definitions');
	}
}
