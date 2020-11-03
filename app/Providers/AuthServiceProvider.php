<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider {

	/**
	 * The policy mappings for the application.
	 *
	 * @var array
	 */
	protected $policies = [
		// 'App\Model' => 'App\Policies\ModelPolicy',
	];

	/**
	 * Register any authentication / authorization services.
	 *
	 * @return void
	 */
	public function boot() {

		$this->registerPolicies();
		Passport::cookie('laravel_admin_token');
		Passport::routes(null, ['prefix' => 'admin/api/oauth']);

		$config = config('auth.oauth');

		Passport::tokensExpireIn(now()->addHours(
			$config['expires_in']['tokens']
		));
		Passport::refreshTokensExpireIn(now()->addHours(
			$config['expires_in']['refresh_tokens']
		));
		Passport::personalAccessTokensExpireIn(now()->addHours(
			$config['expires_in']['personal_access_tokens']
		));
	}
}
