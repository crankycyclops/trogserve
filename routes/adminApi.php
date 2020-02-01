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
Route::get('/statistics', 'AdminApiController@getStatistics');

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

// Uploads a new game definition
Route::post('/definitions', 'AdminApiController@uploadDefinition');

// Return details of a particular game definition
Route::get('/definitions/{id}', 'AdminApiController@getDefinition')
	->where('id', '\d+');

// Delete a particular game definition
Route::delete('/definitions/{id}', 'AdminApiController@deleteDefinition')
	->where('id', '\d+');

// Update one or more meta details of a game definition, or replace the file.
// Using POST here instead of PATCH because of an embarassing PHP issue that
// should have been addressed a long time ago and I'm not going to resort to the
// super lame _method cheat. Seriously, PHP, in 2020? Seriously?
// See: https://github.com/laravel/framework/issues/13457
// And: https://bugs.php.net/bug.php?id=55815
Route::post('/definitions/{id}', 'AdminApiController@modifyDefinition')
	->where('id', '\d+');
