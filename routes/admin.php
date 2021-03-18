<?php

/*
|--------------------------------------------------------------------------
| Admin Web Routes
|--------------------------------------------------------------------------
|
| These are all the routes for the admin portion of the site.
|
*/

$router->pattern('id', '[0-9]+');

// Admin homepage (all admin pages go to the same place because routing is a
// frontend task handled by Vue.js.)
Route::get('/', 'AdminController@index');
Route::get('/state', 'AdminController@index');
Route::get('/state/dump', 'AdminController@index');
Route::get('/state/restore', 'AdminController@index');
Route::get('/state/games', 'AdminController@index');
Route::get('/account', 'AdminController@index');
Route::get('/games', 'AdminController@index');
Route::get('/games/new', 'AdminController@index');
Route::get('games/{id}', 'AdminController@index');
