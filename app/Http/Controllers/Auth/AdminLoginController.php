<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;

class AdminLoginController extends Controller {

	/**
	 * Where to redirect admins to after login.
	 *
	 * @var string
	 */
	static protected $defaultRedirect = '/admin';

	/**
	 * Database field containing the username.
	 *
	 * @var string
	 */
	static protected $usernameField = 'username';

	/**
	 * Database field containing the password.
	 *
	 * @var string
	 */
	static protected $passwordField = 'password';

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct() {

		$this->guard = Auth::guard('adminweb');
	}

	/**
	 * Handle a submitted admin authentication request.
	 *
	 * @param  \Illuminate\Http\Request $request
	 *
	 * @return Response
	 */
	public function auth(Request $request) {

		$credentials = $request->only(self::$usernameField, self::$passwordField);

		if ($this->guard->attempt($credentials)) {
			return redirect()->intended(self::$defaultRedirect);
		}

		else {
			$request->session()->flash('error', trans('auth.failed'));
			return redirect()->route('admin.login');
		}
	}

	/**
	 * Prompt an admin to login.
	 *
	 * @return Response
	 */
	public function login() {

		return view('admin.login');
	}

	/**
	 * Automatically logs out an authenticated admin.
	 *
	 * @param  \Illuminate\Http\Request $request
	 *
	 * @return Response
	 */
	public function logout(Request $request) {

		if ($this->guard->check()) {
			$request->session()->flash('notification', trans('auth.loggedOut'));
			$this->guard->logout();
		}

		return redirect()->route('admin.login');
	}
}
