<?php

namespace Components\Supportstats\Helpers;

require_once Component::path('com_supportstats') . '/vendor/autoload.php';

use Dotenv\Dotenv;

/*
 * Reads configuration variables from the environment for a component
 */
class ClientApiConfigHelper
{

	protected static $_configLoaded = false;
	protected static $_envVariables = array();

	public static function getRedirectUri()
	{
		return self::_getEnvironmentVariable('REDIRECT_URI');
	}

	public static function getSupportLandingPageUrl()
	{
		return self::_getEnvironmentVariable('SUPPORT_LANDING_PAGE_URL');
	}

	public static function getOutstandingTicketsPageUrl()
	{
		return self::_getEnvironmentVariable('OUTSTANDING_TICKETS_PAGE_URL');
	}

	protected static function _getEnvironmentVariable($envVariable)
	{
		if (!in_array($envVariable, self::$_envVariables))
		{
			self::_loadEnvironmentVariable($envVariable);
		}

		return self::$_envVariables[$envVariable];
	}

	protected static function _loadEnvironmentVariable($envVariable)
	{
		if (!self::$_configLoaded)
		{
			self::_loadComponentConfig();
		}

		self::$_envVariables[$envVariable] = getenv($envVariable);
	}

	protected static function _loadComponentConfig()
	{
		$directory = __DIR__ . '/../';
		$envFileName = '.env-com-config';
		$dotenv = new Dotenv($directory, $envFileName);

		$dotenv->load();
		self::$_configLoaded = true;
	}

}
