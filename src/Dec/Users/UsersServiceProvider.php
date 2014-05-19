<?php namespace Dec\Users;

use Dec\Users\Filters\PermissionFilter;
use Dec\Users\Filters\RoleFilter;
use Illuminate\Support\ServiceProvider;

class UsersServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the service provider.
	 *
	 * @return void
	 */
	public function boot()
	{
	    $this->package('dec/users');
	    $this->app['router']->filter('permission', 'users.filter.permission');
	    $this->app['router']->filter('role', 'users.filter.role');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->bindShared('users.filter.permission', function()
		{
		    return new PermissionFilter;
		});

		$this->app->bindShared('users.filter.role', function()
		{
		    return new RoleFilter;
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return [
			'users.filter.permission',
			'users.filter.role'
		];
	}

}
