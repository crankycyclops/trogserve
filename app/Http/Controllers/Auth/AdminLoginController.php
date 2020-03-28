<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AdminLoginController extends Controller {

	/**
	 * Where to redirect admins to after login.
	 *
	 * @var string
	 */
	protected $defaultRedirect = '/admin';

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct() {

		$this->guard = Auth::guard('adminweb');
		$this->middleware('adminauth')->except(['login', 'logout']);
	}

	/**
	 * Handle a submitted admin authentication request.
	 *
	 * @param  \Illuminate\Http\Request $request
	 *
	 * @return Response
	 */
	public function auth(Request $request) {

		$credentials = $request->only('username', 'password');

		if ($this->guard->attempt($credentials)) {
			return redirect()->intended($defaultRedirect);
		}

		else {
			// TODO: set flash error message
			return redirect()->route('admin.login');
		}
	}

	/**
	 * Prompt an admin to login.
	 *
	 * @param  \Illuminate\Http\Request $request
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
	public function logout() {

		if ($this->guard->check()) {
			// TODO: set flash notification message
			$this->guard->logout();
		}

		return redirect()->route('admin.login');
	}
}
