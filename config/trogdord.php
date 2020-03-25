<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Hostname of trogdord instance
	|--------------------------------------------------------------------------
	|
	| This value is the hostname or IP address where the trogdord instance is
	| listening.
	|
	*/

	'host' => env('TROGDORD_HOST', 'localhost'),

	/*
	|--------------------------------------------------------------------------
	| Port of trogdord instance
	|--------------------------------------------------------------------------
	|
	| This value is the port on which the trogdord instance is listening.
	|
	*/

	'port' => env('TROGDORD_PORT', \Trogdord\DEFAULT_PORT),
];
