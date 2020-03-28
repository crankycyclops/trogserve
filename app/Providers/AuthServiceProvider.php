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

		// A less than ideal workaround for the fact that Laravel Passport
		// uses a hardcoded auth provider. This would not be a safe way to
		// implement authentication using multiple providers, but it is a way,
		// at least, for me to use a different name (I might implement a
		// userland api that's separate from the admin in the future, in which
		// case I'll have to revisit my options.) One would have hoped that
		// Laravel Passport would support authentication using multiple
		// providers already, but alas, it does not.
		// See: https://github.com/laravel/passport/issues/161
		// And: https://github.com/laravel/passport/issues/982
		// Workaround is based on this: https://stackoverflow.com/a/52369355/4683164
		config(['auth.guards.api' => config('auth.guards.adminapi')]);

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
