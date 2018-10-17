<?php
return array(
	/*
	|--------------------------------------------------------------------------
	| Ratelimit: Short
	|--------------------------------------------------------------------------
	|
	| Period
	|    The time, in minutes, that a maximum number of requests can be made
	|    by a consumer.
	|
	| Limit
	|    The maximum number of requests that the consumer is permitted to make
	|    per period (time in minutes).
	|
	*/
	'short' => array(
		'period' => '5',
		'limit' => '500'
	),

	/*
	|--------------------------------------------------------------------------
	| Ratelimit: Long
	|--------------------------------------------------------------------------
	|
	| Period
	|    The time, in minutes, that a maximum number of requests can be made
	|    by a consumer.
	|
	| Limit
	|    The maximum number of requests that the consumer is permitted to make
	|    per period (time in minutes).
	|
	*/
	'long' => array(
		'period' => '1440',
		'limit' => '10000'
	),
);
