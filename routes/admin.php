<?php

/*
|--------------------------------------------------------------------------
| Admin Web Routes
|--------------------------------------------------------------------------
|
| These are all the routes for the admin portion of the site.
|
*/

Route::get('/admin', function () {
	return view('admin/index');
});
