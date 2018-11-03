<?php
return array(
	/*
	|--------------------------------------------------------------------------
	| Caching Enabled
	|--------------------------------------------------------------------------
	|
	| Enable or disable caching and set caching level.
	|
	|    0 = Off
	|    1 = Conservative level: Smaller system cache
	|    2 = Progressive level: Faster, bigger system cache, includes module renderers cache.
	|                           Not appropriate for extremely large sites.
	|
	*/
	'caching' => '0',

	/*
	|--------------------------------------------------------------------------
	| Cache Handler
	|--------------------------------------------------------------------------
	|
	| This option controls the default cache "driver" that will be used when
	| using the Caching library. Of course, you may use other drivers any
	| time you wish. This is the default when another is not specified.
	|
	| Supported: "file", "database", "apc", "memcached", "redis", "array"
	|
	*/
	'cache_handler' => 'file',

	/*
	|--------------------------------------------------------------------------
	| File Cache Location
	|--------------------------------------------------------------------------
	|
	| When using the "file" cache driver, we need a location where the cache
	| files may be stored. A sensible default has been specified, but you
	| are free to change it to any other place on disk that you desire.
	|
	*/
	//'path' => '/var/www/hub/app/cache',

	/*
	|--------------------------------------------------------------------------
	| Cache Time
	|--------------------------------------------------------------------------
	|
	| The maximum length of time in minutes for a cache file to be stored
	| before it is refreshed.
	|
	*/
	'cachetime' => '15',

	/*
	|--------------------------------------------------------------------------
	| Memcached Settings
	|--------------------------------------------------------------------------
	|
	| You may specify an array of your Memcached settings that should be
	| used when utilizing the Memcached cache driver. Settings should contain
	| a value for "host", "port", and "weight" options.
	|
	*/
	'memcache_settings' => array(),
);
