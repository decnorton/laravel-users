<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserRolesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Creates `user_roles`
		Schema::create('user_roles', function (Blueprint $table) {
			$table->increments('id')->unsigned();

		    $table->integer('user_id')->unsigned();
		    $table->string('role_key', 32);

		    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
		    $table->foreign('role_key')->references('name')->on('roles')->onDelete('cascade');

		    $table->unique(['user_id', 'role_key']);
		});

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('user_roles', function(Blueprint $table)
		{
			$table->dropUnique('user_roles_user_id_role_key_unique');
			$table->dropForeign('user_roles_user_id_foreign');
			$table->dropForeign('user_roles_role_key_foreign');
		});

		Schema::drop('user_roles');
	}

}
