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

Route::pattern('id', '\d+');

// Return generalized statistics about the game server, such as the number of
// games running.
Route::get('/info', 'AdminApiController@getInfo');

// Return a complete list of running games.
Route::get('/games', 'AdminApiController@getGames');

// Create a new game.
Route::post('/games', 'AdminApiController@createGame');

// Return the details of a particular game.
Route::get('/games/{id}', 'AdminApiController@getGame');

// Destroy a game.
Route::delete('/games/{id}', 'AdminApiController@destroyGame');

// Start (or restart) a game.
Route::get('/games/{id}/start', 'AdminApiController@startGame');

// Stop a game (can be restarted again later.)
Route::get('/games/{id}/stop', 'AdminApiController@stopGame');

// Returns the requested game meta data
Route::get('/games/{id}/meta', 'AdminApiController@getMeta');

// Sets the requested game meta data
Route::post('/games/{id}/meta', 'AdminApiController@setMeta');

// Returns a list of all players in a game
Route::get('/games/{id}/players', 'AdminApiController@getPlayers');

// Creates a player in the specified game
Route::post('/games/{id}/players', 'AdminApiController@createPlayer');

// Removes a player from the specified game
Route::delete('/games/{id}/players', 'AdminApiController@removePlayer');

// Returns a list of all currently uploaded game definition
Route::get('/definitions', 'AdminApiController@getDefinitions');
