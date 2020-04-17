<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;

class FrontendController extends Controller {

	/**
	 * Main page (not a part of the frontend SPA.)
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function index() {

		return view('frontend/index');
	}
}
