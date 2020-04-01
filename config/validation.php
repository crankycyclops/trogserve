<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Constants used to validate calls to /admin/api/games/new
	|--------------------------------------------------------------------------
	|
	| Various constants used to determine valid values for parameters passed
	| when creating a new game.
	|
	*/

	'newGame' => [

		// The name of the game cannot exceed this number of characters
		'nameMaxLen' => 50,
		'nameMaxLenMsg' => 'Name cannot be longer than 50 characters',

		// A game's title cannot exceed this number of characters
		'titleMaxLen' => 100,
		'titleMaxLenMsg' => 'Title cannot be longer than 100 characters',

		// A game's author cannot exceed this number of characters
		'authorMaxLen' => 100,
		'authorMaxLenMsg' => "Author's name cannot be longer than 100 characters",

		// A game's synopsis cannot exceed this number of characters
		'synopsisMaxLen' => 1024,
		'synopsisMaxLenMsg' => 'Synopsis cannot be longer than 1024 characters'
	]
];
