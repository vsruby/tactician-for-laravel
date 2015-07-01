<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Commands Location
	|--------------------------------------------------------------------------
	|
	| Specify the base path for the commands used in the project.
	|
	*/
	'commandNamespace' => 'App\Commands',

	/*
	|--------------------------------------------------------------------------
	| Command Handlers Location
	|--------------------------------------------------------------------------
	|
	| Specify the base path for the command handlers used in the project.
	|
	*/
	'handlerNamespace' => 'App\Handlers\Commands',

	/*
	|--------------------------------------------------------------------------
	| Middleware
	|--------------------------------------------------------------------------
	|
	| Add desired middleware to the command bus. Execution will occur in
	| sequential order.
	|
	*/
	'middleware' => [
		//App/Path/To/Your/Middleware,
		//or Middleware::class
	];
];