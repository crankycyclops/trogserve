<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;

class FrontendController extends Controller {

	/**
	 * Main page (not a part of the frontend SPA.)
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {

		return view('frontend/index');
	}

	/**
	 * All SPA routes will ultimately be served by this action.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function app() {

		return view('frontend/app');
	}
}
