<?php

/*
|--------------------------------------------------------------------------
| Admin Web Routes
|--------------------------------------------------------------------------
|
| These are all the routes for the admin portion of the site.
|
*/

// Admin homepage
Route::get('/', function () {
	return view('admin/index');
});
