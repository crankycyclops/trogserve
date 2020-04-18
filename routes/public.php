<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

$router->pattern('id', '[0-9]+');

// Home page
Route::get('/', 'FrontendController@index')->name('frontend.index');

// SPA route: list games
Route::get('/games', 'FrontendController@app');

// SPA route: load a specific game
Route::get('/games/{id}', 'FrontendController@app');
