<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Admin API Routes
|--------------------------------------------------------------------------
|
| Basic admin functionality is implemented via a series of api calls. This
| allows for some extra flexibility in how game activity is moderated.
*/

// Return generalized statistics about the game server, such as the number of
// games running.
Route::get('/info', 'AdminApiController@getInfo');

// Return a complete list of running games.
Route::get('/games', 'AdminApiController@getGames');

// Create a new game.
Route::post('/games', 'AdminApiController@createGame');

// Return the details of a particular game.
Route::get('/games/{id}', 'AdminApiController@getGame')
	->where('id', '\d+');

// Destroy a game.
Route::delete('/games/{id}', 'AdminApiController@destroyGame')
	->where('id', '\d+');

// Start (or restart) a game.
Route::get('/games/{id}/start', 'AdminApiController@startGame')
	->where('id', '\d+');

// Stop a game (can be restarted again later.)
Route::get('/games/{id}/stop', 'AdminApiController@stopGame')
	->where('id', '\d+');

// Returns a list of all currently uploaded game definition
Route::get('/definitions', 'AdminApiController@getDefinitions');
