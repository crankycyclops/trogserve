<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Public API Routes
|--------------------------------------------------------------------------
|
| Public API calls that drive the frontend application.
*/

Route::pattern('id', '\d+');

// Return a complete list of running games.
Route::get('/games', 'ApiController@getGames');

// Return publicly accessible details of a running game.
Route::get('/games/{id}', 'ApiController@getGame');
