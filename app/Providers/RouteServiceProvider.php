<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider {

	/**
	 * This namespace is applied to your controller routes.
	 *
	 * In addition, it is set as the URL generator's root namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'App\Http\Controllers';

	/**
	 * The path to the "home" route for your application.
	 *
	 * @var string
	 */
	public const HOME = '/home';

	/**
	 * Define your route model bindings, pattern filters, etc.
	 *
	 * @return void
	 */
	public function boot() {

		parent::boot();
	}

	/**
	 * Define the routes for the application.
	 *
	 * @return void
	 */
	public function map() {

		$this->mapWebRoutes();
		$this->mapAdminApiRoutes();
		$this->mapAdminWebRoutes();
	}

	/**
	 * Define routes for the non-admin portion of the application.
	 *
	 * These routes all receive session state, CSRF protection, etc.
	 *
	 * @return void
	 */
	protected function mapWebRoutes() {

		Route::middleware('web')
			->namespace($this->namespace)
			->group(base_path('routes/web.php'));
	}

	/**
	 * Define routes for the admin portion of the application.
	 *
	 * These routes all receive session state, CSRF protection, etc.
	 *
	 * @return void
	 */
	protected function mapAdminWebRoutes() {

		Route::middleware('adminweb')
			->namespace($this->namespace)
			->group(base_path('routes/adminWeb.php'));
	}

	/**
	 * Define the "admin/api" routes for the application.
	 *
	 * These routes are typically stateless.
	 *
	 * @return void
	 */
	protected function mapAdminApiRoutes() {

		Route::prefix('admin/api')
			->middleware('adminapi')
			->namespace($this->namespace)
			->group(base_path('routes/adminApi.php'));
	}
}
