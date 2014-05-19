<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRolePermissionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Create `role_permissions`
		Schema::create('role_permissions', function (Blueprint $table) {
		    $table->increments('id')->unsigned();

		    $table->string('role_key', 32);
		    $table->string('permission_key', 32);

		    $table->foreign('role_key')->references('name')->on('roles')->onDelete('cascade');
		    $table->foreign('permission_key')->references('name')->on('permissions')->onDelete('cascade');

		    $table->unique(['role_key', 'permission_key']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('role_permissions', function(Blueprint $table)
		{
			$table->dropUnique('role_permissions_role_key_permission_key_unique');
			$table->dropForeign('role_permissions_role_key_foreign');
			$table->dropForeign('role_permissions_permission_key_foreign');
		});

		Schema::drop('role_permissions');
	}

}
