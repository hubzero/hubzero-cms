<?php

namespace Components\Supportstats\Helpers;

use Components\Supportstats\Models\HubAuthorization;

class HubAuthorizationFactory
{

	public static function create($instanceData)
	{
		$hubAuthorization = new HubAuthorization();
		self::addCreatedDate($instanceData);

		self::update($hubAuthorization, $instanceData);

		return $hubAuthorization;
	}

	public static function update($hubAuthorization, $instanceData)
	{
		foreach ($instanceData as $property => $value)
		{
			$hubAuthorization->set($property, $value);
		}

		$hubAuthorization->save();
	}

	protected static function addCreatedDate(&$instanceData)
	{
		self::addCurrentDate('created', $instanceData);
	}

	protected static function addModifiedDate(&$instanceData)
	{
		self::addCurrentDate('modified', $instanceData);
	}

	protected static function addCurrentDate($key, &$instanceData)
	{
		if (!array_key_exists($key, $instanceData))
		{
			$instanceData[$key] = Date::of('now')->toSql();
		}
	}

}
