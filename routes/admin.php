<?php

/*
|--------------------------------------------------------------------------
| Admin Web Routes
|--------------------------------------------------------------------------
|
| These are all the routes for the admin portion of the site.
|
*/

// Admin homepage (all admin pages go to the same place because routing is a
// frontend task that's handled by Vue.js.)
Route::get('/', 'AdminController@index');
Route::get('/games', 'AdminController@index');
