<?php
return array(
	/*
	|--------------------------------------------------------------------------
	| Session Handler
	|--------------------------------------------------------------------------
	|
	| The mechanism by which the hub identifies a User once they are connected
	| to the web site using non-persistent cookies.
	|
	| Supported: "file", "cookie", "database", "apc",
	|            "memcached", "redis", "array"
	|
	*/
	'session_handler' => 'database',

	/*
	|--------------------------------------------------------------------------
	| Session Lifetime
	|--------------------------------------------------------------------------
	|
	| Here you may specify the number of minutes that you wish the session
	| to be allowed to remain idle before it expires.
	|
	*/
	'lifetime' => '45',

	/*
	|--------------------------------------------------------------------------
	| Session Cookie Path
	|--------------------------------------------------------------------------
	|
	| The session cookie path determines the path for which the cookie will
	| be regarded as available. Typically, this will be the root path of
	| your application but you are free to change this when necessary.
	|
	*/
	'cookie_path' => '',

	/*
	|--------------------------------------------------------------------------
	| Session Cookie Domain
	|--------------------------------------------------------------------------
	|
	| Here you may change the domain of the cookie used to identify a session
	| in your application. This will determine which domains the cookie is
	| available to in your application. A sensible default has been set.
	|
	*/
	'cookie_domain' => '',
	'cookiesubdomains' => '0',
);
