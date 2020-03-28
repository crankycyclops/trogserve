<?php

/*
|--------------------------------------------------------------------------
| Admin Authentication Web Routes
|--------------------------------------------------------------------------
|
| These are all the admin authentication routes.
|
*/

// Handles actual authentication request
Route::post('/auth', 'Auth\AdminLoginController@auth')->name('admin.auth');

// Admin login prompt
Route::get('/login', 'Auth\AdminLoginController@login')->name('admin.login');

// Admin logout
Route::get('/logout', 'Auth\AdminLoginController@logout')->name('admin.logout');
