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
Route::pattern('slot', '\d+');

// Return a list of all non-sensitive settings set in trogdord.ini.
Route::get('/config', 'AdminApiController@getConfig');

// Return generalized statistics about the game server, such as the number of
// games running.
Route::get('/info', 'AdminApiController@getInfo');

// Dumps the server's state to disk
Route::post('/dump', 'AdminApiController@dump');

// Restores trogdord's state from a dump
Route::post('/restore', 'AdminApiController@restore');

// Returns a list of all currently uploaded game definition
Route::get('/definitions', 'AdminApiController@getDefinitions');

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
Route::delete('/games/{id}/players/{name}', 'AdminApiController@removePlayer');

// Dumps a game's state to disk
Route::post('/games/{id}/dump', 'AdminApiController@dumpGame');

// Retrieve a list of game dumps
Route::get('/dumps', 'AdminApiController@getDumps');

// Retrieve information about a specific game dump
Route::get('/dumps/{id}', 'AdminApiController@getDump');

// Deletes a game dump
Route::delete('/dumps/{id}', 'AdminApiController@deleteDump');

// Restores a game dump (restores the most recent slot)
Route::post('/dumps/{id}/restore', 'AdminApiController@restoreDump');

// Retrieves a list of all slots belonging to a game dump
Route::get('/dump/{id}/slots', 'AdminApiController@getSlots');

// Retrieves details of a specific dump slot
Route::get('/dump/{id}/slots/{slot}', 'AdminApiController@getSlot');

// Deletes a specific dump slot
Route::delete('/dump/{id}/slots/{slot}', 'AdminApiController@deleteSlot');

// Restores a specific dump slot
Route::post('/dump/{id}/slots/{slot}/restore', 'AdminApiController@restoreSlot');
