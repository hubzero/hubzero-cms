<?php

namespace Components\Supportstats\Helpers;

use GuzzleHttp\Client;

class ApiHelper
{

	protected static $httpClient = null;

	public static function get($url)
	{
		return self::_sendRequest('get', $url);
	}

	public static function post($url, $params)
	{
		return self::_sendRequest('post', $url, $params);
	}

	protected static function _sendRequest($requestMethod, $url, $params = array())
	{
		$httpClient = self::_getHttpClient();

		$response = $httpClient->$requestMethod($url, $params);

		return $response->json();
	}

	protected static function _getHttpClient($params = array(
		'defaults' => array('verify' => false)
	))
	{
		if (!self::$httpClient)
		{
			self::$httpClient = new Client($params);
		}

		return self::$httpClient;
	}


}
