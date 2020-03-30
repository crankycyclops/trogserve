<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;

class AdminController extends Controller {

	/**
	 * All frontend page requests end up invoking this controller action (Vue.js
	 * will handle routing within the admin SPA.)
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function index() {

		return view('admin/index');
	}
}
