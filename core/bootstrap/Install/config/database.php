<?php
return array(
	/*
	|--------------------------------------------------------------------------
	| Database Driver
	|--------------------------------------------------------------------------
	|
	| The type of database in use.
	|
	*/
	'dbtype' => 'pdo',

	/*
	|--------------------------------------------------------------------------
	| Database Host
	|--------------------------------------------------------------------------
	|
	| The hostname for your database. For most installs this will be
	| 'localhost' (127.0.0.1)
	|
	*/
	'host' => 'localhost',

	/*
	|--------------------------------------------------------------------------
	| Database Credentials
	|--------------------------------------------------------------------------
	|
	| The username for access to your database.
	|
	*/
	'user' => '',
	'password' => '',

	/*
	|--------------------------------------------------------------------------
	| Database Name
	|--------------------------------------------------------------------------
	|
	| The name for your database.
	|
	*/
	'db' => 'hub',

	/*
	|--------------------------------------------------------------------------
	| Database Character Set
	|--------------------------------------------------------------------------
	|
	| Characterset the database should use. Default is UTF8. It is strongly
	| recommended this is not changed.
	|
	*/
	//'dbcharset' => 'utf8',
	//'dbcollation' => '',

	/*
	|--------------------------------------------------------------------------
	| Database Table Prefix
	|--------------------------------------------------------------------------
	|
	| The prefix used for your database tables.
	|
	| While it is strongly recommended that the hub have its own dedicated
	| database, this is used for namespacing tables used by the hub to avoid
	| potential collision with tables being used by other packages.
	|
	*/
	'dbprefix' => 'jos_',
);
