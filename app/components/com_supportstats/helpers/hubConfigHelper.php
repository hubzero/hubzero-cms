<?php

namespace Components\Supportstats\Helpers;

require_once Component::path('com_supportstats') . '/vendor/autoload.php';

use Dotenv\Dotenv;

class HubConfigHelper
{

	public static function getAccessToken($hubName)
	{
		self::_loadApiCredentials($hubName);
		$envVarName = self::_getEnvVarName($hubName, 'ACCESS_TOKEN');
		$accessToken = getenv($envVarName);

		return $accessToken;
	}

	protected static function _loadApiCredentials($hubName)
	{
		$directory = __DIR__ . '/../';
		$envFileName = self::_getEnvFileName($hubName);
		$dotenv = new Dotenv($directory, $envFileName);

		$dotenv->load();
	}

	protected static function _getEnvFileName($hubName)
	{
		$formattedName = self::_getFormattedName($hubName);
		$envFileName = '.env-' . strtolower($formattedName);

		return $envFileName;
	}

	protected static function _getEnvVarName($hubName, $envVar)
	{
		$formattedName = self::_getFormattedName($hubName);
		$envVarName = strtoupper($hubName) . "_$envVar";

		return $envVarName;
	}

	protected static function _getFormattedName($hubName)
	{
		return preg_replace("/ /", '', $hubName);
	}

}
