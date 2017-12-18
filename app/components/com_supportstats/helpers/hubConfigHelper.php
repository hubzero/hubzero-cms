<?php

namespace Components\Supportstats\Helpers;

require_once Component::path('com_supportstats') . '/vendor/autoload.php';

use Dotenv\Dotenv;

class HubConfigHelper
{

	public static function setApiCredentials($hub)
	{
		self::_loadApiCredentials($hub);
		$credentialList = self::_getApiCredentialList($hub);

		foreach($credentialList as $instanceVarName => $envVarName)
		{
			$hub->$instanceVarName = getenv($envVarName);
		}
	}

	protected static function _loadApiCredentials($hub)
	{
		$directory = __DIR__ . '/../';
		$formattedName = self::_getFormattedName($hub);
		$envFileName = '.env-' . strtolower($formattedName);
		$dotenv = new Dotenv($directory, $envFileName);

		$dotenv->load();
	}

	protected static function _getApiCredentialList($hub)
	{
		$formattedHubName = strtoupper(self::_getFormattedName($hub));

		return array(
			'_apiClientId' => $formattedHubName . '_CLIENT_ID',
			'_apiClientSecret' => $formattedHubName . '_CLIENT_SECRET'
		);
	}

	protected static function _getFormattedName($hub)
	{
		return preg_replace("/ /", '', $hub->get('name'));
	}

}
