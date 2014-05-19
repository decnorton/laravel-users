<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Create `users`
		Schema::create('users', function ($table) {
		    $table->increments('id')->unsigned();

		    $table->string('username')->unique();
		    $table->string('email')->unique();
		    $table->string('name')->nullable();
		    $table->string('password');
		    $table->string('remember_token', 100)->nullable();

		    $table->timestamps();
		    $table->softDeletes();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('users');
	}

}
