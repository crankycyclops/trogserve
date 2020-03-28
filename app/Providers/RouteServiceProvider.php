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

		$this->mapPublicRoutes();
		$this->mapAdminRoutes();
		$this->mapAdminApiRoutes();
	}

	/**
	 * Define routes for the non-admin portion of the application.
	 *
	 * These routes all receive session state, CSRF protection, etc.
	 *
	 * @return void
	 */
	protected function mapPublicRoutes() {

		Route::middleware('public')
			->namespace($this->namespace)
			->group(base_path('routes/public.php'));
	}

	/**
	 * Define routes for the admin portion of the application.
	 *
	 * These routes all receive session state, CSRF protection, etc.
	 *
	 * @return void
	 */
	protected function mapAdminRoutes() {

		// Admin authentication pages
		Route::prefix('admin')
			->middleware('public')
			->namespace($this->namespace)
			->group(base_path('routes/adminAuth.php'));

		// Admin pages requiring prior authentication
		Route::prefix('admin')
			->middleware('admin')
			->namespace($this->namespace)
			->group(base_path('routes/admin.php'));
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
